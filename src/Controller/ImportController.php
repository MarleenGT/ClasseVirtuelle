<?php


namespace App\Controller;


use App\Controller\Message\Email;
use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Matieres;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use App\Entity\Users;
use App\Service\CompleteUser;
use App\Service\ImportCsv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/Importer")
 */
class ImportController extends AbstractController
{
    /**
     * @Route ("", name="import.import", methods={"POST"})
     * @param Request $request
     * @param CompleteUser $completeUser
     * @param ImportCsv $importCsv
     * @param MessageBusInterface $messageBus
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function import(Request $request, CompleteUser $completeUser, ImportCsv $importCsv, MessageBusInterface $messageBus)
    {
        $files = $request->files->all();
        $array = array_reverse($files);
        $file = array_pop($array);
        if ($file === null) {
            $this->addFlash('danger', 'Fichier non trouvé.');
            return $this->redirectToRoute('reglages.index');
        }
        $str = file_get_contents($file->getPathname());
        $str = filter_var(str_replace(";", ",", $str), FILTER_SANITIZE_STRING);
        $em = $this->getDoctrine()->getManager();

        switch (array_key_first($files)) {

            case "Classes":
                $classes = $importCsv->import($str, ['NOM']);
                foreach ($classes as $classe) {
                    $obj = new Classes();
                    $sgA = new SousGroupes();
                    $sgB = new SousGroupes();
                    $obj->setNomClasse($classe["NOM"]);
                    $sgA->setNomSousgroupe($classe["NOM"] . ' Groupe 1');
                    $sgB->setNomSousgroupe($classe["NOM"] . ' Groupe 2');
                    $em->persist($obj);
                    $em->persist($sgA);
                    $em->persist($sgB);
                }
                $this->addFlash('success', 'Classes importées !');
                break;

            case "Professeurs":
                $profs = $importCsv->import($str, ['NOM', 'PRENOM', 'EMAIL'], ['CIVILITE', 'DISCIPLINE']);
                $list_matiere = [];
                foreach ($profs as $prof) {
                    $obj = new Profs();
                    $obj->setNom(htmlspecialchars_decode($prof["NOM"], ENT_QUOTES))->setPrenom(htmlspecialchars_decode($prof["PRENOM"], ENT_QUOTES))->setIdUser(new Users());
                    $obj->getIdUser()->setEmail($prof['EMAIL']);
                    if (isset($prof['CIVILITE'])) {
                        $obj->setCivilite($prof["CIVILITE"]);
                    }
                    if (isset($prof['DISCIPLINE'])) {
                        $arr_matiere = explode(' ', $prof['DISCIPLINE'], 2);
                        $query = $this->getDoctrine()->getRepository(Matieres::class)->findOneBy(['libelle' => $arr_matiere[0]]);
                        if (!$query) {
                            if (isset($list_matiere[$arr_matiere[0]])) {
                                $query = $list_matiere[$arr_matiere[0]];
                            } else {
                                $query = new Matieres();
                                $query->setLibelle($arr_matiere[0])->setNomMatiere($arr_matiere[1]);
                                $em->persist($query);
                                $list_matiere[$query->getLibelle()] = $query;
                            }
                        }
                        $obj->addIdMatiere($query);
                    }
                    $obj = $completeUser->completeUser($obj, "Professeurs");
                    $em->persist($obj);
                    $email = new Email();
                    $url = $this->generateUrl('account.change', ['email' => $obj->getIdUser()->getEmail(), 'token' => $obj->getIdUser()->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
                    $email->setTask($obj);
                    $email->setUrl($url);
                    $messageBus->dispatch($email);
                }
                $this->addFlash('success', 'Professeurs importés !');
                break;

            case "Élèves":
                $eleves = $importCsv->import($str, ['NOM', 'PRENOM', 'EMAIL', 'CLASSES']);
                foreach ($eleves as $eleve) {
                    $obj = new Eleves();
                    $classe = $this->getDoctrine()->getRepository(Classes::class)->findOneBy(['nom_classe' => $eleve['CLASSES']]);
                    if ($classe) {
                        $obj->setNom(htmlspecialchars_decode($eleve["NOM"], ENT_QUOTES))->setPrenom(htmlspecialchars_decode($eleve["PRENOM"], ENT_QUOTES))->setIdClasse($classe)->setIdUser(new Users());
                        $obj->getIdUser()->setEmail($eleve['EMAIL']);
                        $obj = $completeUser->completeUser($obj, "Eleves");
                        $email = new Email();
                        $url = $this->generateUrl('account.change', ['email' => $obj->getIdUser()->getEmail(), 'token' => $obj->getIdUser()->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
                        $email->setTask($obj);
                        $email->setUrl($url);
                        $messageBus->dispatch($email);
                        $em->persist($obj);
                    } else {
                        $this->addFlash('danger', 'Il manque la classe (' . $eleve['CLASSES'] . ') de ' . $obj->getNom() . ' ' . $obj->getPrenom() . ". Il n'a pas été importé.");
                        $str .= "La classe" . $eleve['CLASSES'] . " de " . $obj->getNom() . " " . $obj->getPrenom() . ". L'utilisateur n'a pas été importé. <br>";
                    }

                }
                $this->addFlash('success', 'Éleves importés !');
        };
        $em->flush();

        return $this->redirectToRoute('reglages.index');

    }


}
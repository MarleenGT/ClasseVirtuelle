<?php


namespace App\Controller;


use App\Controller\Message\Email;
use App\Entity\Classes;
use App\Entity\Eleves;
use App\Entity\Matieres;
use App\Entity\Profs;
use App\Entity\Users;
use App\Service\CompleteUser;
use App\Service\ImportCsv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return Response
     */
    public function import(Request $request, CompleteUser $completeUser, ImportCsv $importCsv, MessageBusInterface $messageBus)
    {
        $str = "";
        $files = $request->files->all();
        $array = array_reverse($files);
        $str = file_get_contents(array_pop($array)->getPathname());
        $str = filter_var(str_replace(";", ",", $str), FILTER_SANITIZE_STRING);
        $em = $this->getDoctrine()->getManager();
        switch (array_key_first($files)) {
            case "importClasses":
                $classes = $importCsv->import($str, ['NOM']);
                foreach ($classes as $class) {
                    $obj = new Classes();
                    $obj->setNomClasse($class);
                    $em->persist($obj);
                }
                $this->addFlash('success', 'Classes importées !');
                break;
            case "importProfs":
                $profs = $importCsv->import($str, ['NOM', 'PRENOM', 'EMAIL'], ['CIVILITE', 'DISCIPLINE']);
                $list_matiere = [];
                foreach ($profs as $prof) {
                    $obj = new Profs();
                    $obj->setNom($prof["NOM"])->setPrenom($prof["PRENOM"])->setIdUser(new Users());
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
                    $email = new Email();
                    $email->setTask($obj);
                    $messageBus->dispatch($email);
                    $em->persist($obj);
                }
                $this->addFlash('success', 'Professeurs importés !');
                break;
            case "importEleves":
                $eleves = $importCsv->import($str, ['NOM', 'PRENOM', 'EMAIL', 'CLASSES']);
                foreach ($eleves as $eleve) {
                    $obj = new Eleves();
                    $classe = $this->getDoctrine()->getRepository(Classes::class)->findOneBy(['nom_classe' => $eleve['CLASSES']]);
                    if ($classe) {
                        $obj->setNom($eleve['NOM'])->setPrenom($eleve['PRENOM'])->setIdClasse($classe)->setIdUser(new Users());
                        $obj->getIdUser()->setEmail($eleve['EMAIL']);
                        $obj = $completeUser->completeUser($obj, "Eleves");
                    } else {
                        $str .= "La classe" . $eleve['CLASSES'] . " de " . $obj->getNom() . " " . $obj->getPrenom() . ". L'utilisateur n'a pas été importé. <br>";
                    }
                    $email = new Email();
                    $email->setTask($obj);
                    $messageBus->dispatch($email);
                    $em->persist($obj);
                }
                $this->addFlash('success', 'Éleves importés !');
        };
        $em->flush();
        return $this->render('settings/settings.html.twig', [
            'error' => $str
        ]);

    }


}
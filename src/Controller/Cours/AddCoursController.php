<?php


namespace App\Controller\Cours;


use App\Entity\Cours;
use App\Entity\Eleves;
use App\Entity\Profs;
use App\Entity\Sousgroupes;
use App\Form\Cours\CoursType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AddCoursController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_PROF")
     * @Route("/Cours/Ajouter", name="cours.add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $session = new Session();
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours, [
            'matieres' => $session->get('matiere')->getValues(),
            'classes' => $session->get('classe')->getValues(),

        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prof = $this->getDoctrine()->getRepository(Profs::class)->find($session->get('id'));
            $obj = $form->getData();
            if (($obj->getIdClasse() === null && $obj->getIdSousgroupe() === null) || ($obj->getIdClasse() !== null && $obj->getIdSousgroupe() !== null)){
                return $this->render("cours/add.html.twig",[
                    "form" => $form->createView(),
                    'error' => 'Rentrez une classe ou un sous-groupe.'
                ]);
            }
            /*
             * Partie nécessaire pour que les entités Classes et Matières de l'entité Cours soient les mêmes
             * que celles du prof. Sinon, Doctrine cherche à rajouter des entités supplémentaires dans les tables.
             */
            $id_matiere = $obj->getIdMatiere()->getId();
            $id_classe = $obj->getIdClasse()->getId();
            foreach ($prof->getIdMatiere()->getValues() as $matiere){
                if ($matiere->getId() === $id_matiere){
                    $obj->setIdMatiere($matiere);
                    break;
                }
            }
            foreach ($prof->getIdClasse()->getValues() as $classe){
                if ($classe->getId() === $id_classe){
                    $obj->setIdClasse($classe);
                    break;
                }
            }
            /*
             * Création des objets DateTime pour le début et fin du cours
             */
            $date = $form['date']->getData();
            $heure_debut = $form['heure_debut']->getData()->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $heure_fin = $form['heure_fin']->getData()->setDate($date->format('Y'), $date->format('m'), $date->format('d'));

            $obj->setDate(new DateTime())->setIdProf($prof)->setHeureDebut($heure_debut)->setHeureFin($heure_fin);
            try {
                $verif = $this->verifCours($obj);
            } catch (Exception $e){

            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->redirectToRoute('cours.index');
        }
        return $this->render("cours/add.html.twig",[
            "form" => $form->createView()
            ]);
    }

    private function verifCours(Cours $cours)
    {
        $heure_debut = $cours->getHeureDebut();
        $heure_fin = $cours->getHeureFin();
        $coursListe = [];
        $sousgroupeListe = [];
        if ($cours->getIdClasse()){


            $classe = $cours->getIdClasse();
            $eleves = $this->getDoctrine()->getRepository(Eleves::class)->findElevesByClasse($classe);
            foreach ($eleves as $eleve){
                $sousgroupes = $this->getDoctrine()->getRepository(Sousgroupes::class)->findSousgroupesByEleve($eleve);
                if(count($sousgroupes) > 0){
                    foreach ($sousgroupes as $sousgroupe){
                        if(!array_search($sousgroupe, $sousgroupeListe)){
                            $coursSousGroupeListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursBySousgroupe($sousgroupe, $heure_debut, $heure_fin);
                            foreach ($coursSousGroupeListe as $coursSousgroupe){
                                $coursListe[] = $coursSousgroupe;
                            }
                        }

                    }
                }
                $coursEleveListe = $this->getDoctrine()->getRepository(Cours::class)->findCoursByClasse($classe, $heure_debut, $heure_fin);
                foreach ($coursEleveListe as $coursEleve){
                    $coursListe[] = $coursEleve;
                }
            }
        } elseif ($cours->getIdSousgroupe()){
            $groupe = $cours->getIdSousgroupe();
        } else {
            throw new Exception("Classe et sous-groupe nuls pour le cours à ajouter.");
        }

    }
}
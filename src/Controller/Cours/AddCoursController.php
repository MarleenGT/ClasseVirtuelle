<?php


namespace App\Controller\Cours;


use App\Entity\Cours;
use App\Entity\Profs;
use App\Form\Cours\CoursType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        dump(spl_object_hash($session->get('matiere')->getValues()[0]));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prof = $this->getDoctrine()->getRepository(Profs::class)->find($session->get('id'));
            $obj = $form->getData();
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
            $obj->setDate(new DateTime())->setIdProf($prof);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->redirectToRoute('cours.index');
        }
        return $this->render("cours/add.html.twig",[
            "form" => $form->createView()
            ]);
    }
}
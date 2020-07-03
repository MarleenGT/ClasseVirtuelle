<?php


namespace App\Service;


use App\Entity\Archives;
use App\Entity\Cours;
use App\Entity\DateArchive;
use DateTime;
use ReflectionException;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckArchive extends AbstractController
{
    /**
     * Vérifie la date du dernier archivage des cours et lance l'archivage si besoin.
     */
    public function check()
    {
        $date = $this->getDoctrine()->getRepository(DateArchive::class)->find(1);
        $today = new DateTime();
        if ($date->getDateDerniereArchive()->format('W') < $today->format('W')) {
            $error = $this->remove($today);
            if (!$error){
                $date->setDateDerniereArchive($today);

            }
        }
    }

    /**
     * @param $cours
     * @return Archives
     * @throws ReflectionException
     */
    private function transformCoursToArchive($cours): Archives
    {
        $archive = new Archives();
        $coursReflection = new ReflectionObject($cours);
        $archiveReflection = new ReflectionObject($archive);
        foreach ($coursReflection->getProperties() as $property) {
            if ($archiveReflection->hasProperty($property->getName()) && $property->getName() !== 'id') {
                $archiveProperty = $archiveReflection->getProperty($property->getName());
                $archiveProperty->setAccessible(true);
                $property->setAccessible(true);
                $archiveProperty->setValue($archive, $property->getValue($cours));
            }
        }
        return $archive;
    }

    /**
     * @param DateTime $today
     */
    private function remove(DateTime $today)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $coursArray = $this->getDoctrine()->getRepository(Cours::class)->findCoursToArchive($today);
        foreach ($coursArray as $cours) {
            try {
                $archive = $this->transformCoursToArchive($cours);
            } catch (ReflectionException $e) {
                return 'Problème lors de l\'archivage des anciens cours. Veuillez contacter l\'administrateur.';
            }
            $entityManager->persist($archive);
            $entityManager->remove($cours);
        }
        $entityManager->flush();
        return false;
    }
}
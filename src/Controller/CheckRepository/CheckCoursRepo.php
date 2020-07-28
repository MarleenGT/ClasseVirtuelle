<?php


namespace App\Controller\CheckRepository;


use App\Entity\Archives;
use App\Entity\Cours;
use App\Entity\DateArchive;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class CheckCoursRepo
 * @package App\Controller\CheckRepository
 * Récupère le répertoire contenant les cours de la semaine
 */
class CheckCoursRepo extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function check(int $timeLundi)
    {
        $timeSamedi = $timeSamedi = $timeLundi + 5 * 24 * 60 * 60 + 60 * 60 * ($this->getParameter('endTimeTable') - $this->getParameter('startTimeTable'));
        $date_samedi = (new DateTime)->setTimestamp($timeSamedi);

        $archivage = $this->em->getRepository(DateArchive::class)->find(1)->getDateDerniereArchive();
        return ($archivage < $date_samedi)? Cours::class : Archives::class;
    }
}
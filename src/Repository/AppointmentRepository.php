<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * @return Appointment[] Returns an array of Appointment objects
     */
    public function findByUserOrCreatedBy(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->orWhere('a.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('a.appointmentTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find an appointment by slot
     */
    public function findOneBySlot(string $start, string $end): ?Appointment
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.appointmentTime < :end')
            ->andWhere('a.appointmentTime > :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findFutureAppointments(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.appointmentTime > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.appointmentTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Appointment[] Returns an array of Appointment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Appointment
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

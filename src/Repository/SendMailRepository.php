<?php

namespace App\Repository;

use App\Entity\SendMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SendMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method SendMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method SendMail[]    findAll()
 * @method SendMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SendMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SendMail::class);
    }

    // /**
    //  * @return SendMail[] Returns an array of SendMail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SendMail
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

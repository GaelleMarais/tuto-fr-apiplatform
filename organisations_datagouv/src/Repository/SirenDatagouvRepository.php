<?php

namespace App\Repository;

use App\Entity\SirenDatagouv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SirenDatagouv|null find($id, $lockMode = null, $lockVersion = null)
 * @method SirenDatagouv|null findOneBy(array $criteria, array $orderBy = null)
 * @method SirenDatagouv[]    findAll()
 * @method SirenDatagouv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SirenDatagouvRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SirenDatagouv::class);
    }

    // /**
    //  * @return SirenDatagouv[] Returns an array of SirenDatagouv objects
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
    public function findOneBySomeField($value): ?SirenDatagouv
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

<?php

namespace App\Repository;

use App\Entity\StripeTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StripeTrait>
 *
 * @method StripeTrait|null find($id, $lockMode = null, $lockVersion = null)
 * @method StripeTrait|null findOneBy(array $criteria, array $orderBy = null)
 * @method StripeTrait[]    findAll()
 * @method StripeTrait[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StripeTraitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StripeTrait::class);
    }

//    /**
//     * @return StripeTrait[] Returns an array of StripeTrait objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StripeTrait
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

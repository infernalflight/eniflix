<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Response;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findBySeveralCriterias(string $status, int $offset, int $nbParPage): array
    {
        $query = $this->createQueryBuilder('s')
            ->setMaxResults($nbParPage)
            ->setFirstResult($offset)
            ->orderBy('s.name', 'ASC')
            ->andWhere('s.genres like :genre1 AND s.genres like :genre2')
            ->setParameter('genre1', '%drama%')
            ->setParameter('genre2', '%mystery%')
            ->andWhere('s.firstAirDate >= :dateSeuil')
            ->setParameter('dateSeuil', new \DateTime('-6 years'))
        ;

        if ($status !== 'all') {
            $query->andWhere('s.status = :status')
                ->setParameter('status', $status);
        }

        return $query->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Serie[] Returns an array of Serie objects
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

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

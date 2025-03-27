<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getWithDql(string $status, int $offset, int $nbParPage): array
    {
        $dql = "SELECT s FROM App\Entity\Serie s 
                WHERE (s.genres LIKE :genre1 OR s.genres LIKE :genre2)
                AND s.firstAirDate >= :dateSeuil";

        if ($status !== 'all') {
            $dql .= " AND s.status = :status";
        }

        $dql .= " ORDER BY s.name ASC";

        $q = $this->getEntityManager()->createQuery($dql)
            ->setParameter('genre1', '%mystery%')
            ->setParameter('genre2', '%drama%')
            ->setParameter('dateSeuil', new \DateTime('-6 years'));

        if ($status !== 'all') {
            $q->setParameter("status", $status);
        }

        return $q->setMaxResults($nbParPage)->setFirstResult($offset)->execute();
    }

    public function findWithRawSql(int $offset, int $nbParPage): array
    {
        $sql = "SELECT * FROM serie s 
         WHERE (s.genres LIKE :genre1 OR s.genres LIKE :genre2)
         AND s.first_air_date >= :dateSeuil
         LIMIT $nbParPage OFFSET $offset";

        return $this->getEntityManager()->getConnection()
            ->prepare($sql)
            ->executeQuery([
                'genre1' => '%mystery%',
                'genre2' => '%drama%',
                'dateSeuil' => (new \DateTime('-6 years'))->format('Y-m-d'),
            ])
            ->fetchAllAssociative();

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

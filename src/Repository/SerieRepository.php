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

    public function findSeriesCustom(float $popularity, float $vote): array
    {
        // Utilisation du QueryBuilder
        return $this->createQueryBuilder('s')
            ->andWhere('s.popularity > :popularity OR s.firstAirDate > :date')
            ->andWhere('s.vote < :vote')
            ->orderBy('s.popularity', 'DESC')
            ->addOrderBy('s.firstAirDate', 'DESC')
            ->setParameter('popularity', $popularity)
            ->setParameter('vote', $vote)
            ->setParameter('date', new \DateTime('- 5 years'))
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // Utilisation du DQL
    public function findSeriesWithDQL(float $popularity, float $vote): array
    {
        $dql = <<<SQL
                SELECT s FROM App\Entity\Serie s 
                WHERE (s.popularity > :popularity OR s.firstAirDate > :date) AND s.vote > :vote
                ORDER BY s.popularity DESC, s.firstAirDate DESC
              SQL;
        return $this->getEntityManager()->createQuery($dql)
            ->setMaxResults(10)
            ->setFirstResult(0)
            ->setParameter('popularity', $popularity)
            ->setParameter('vote', $vote)
            ->setParameter('date', new \DateTime('- 5 years'))
            ->execute();
    }

    // Utilisation du Raw SQL
    public function findSeriesWithSQL(float $popularity, float $vote): array
    {
        $sql = <<<SQL
            SELECT * FROM serie s
                     WHERE (s.popularity > :popularity OR s.first_air_date > :date)
                     AND s.vote > :vote
                    ORDER BY s.popularity DESC, s.first_air_date DESC
                    LIMIT 10 OFFSET 0
          SQL;

        $conn = $this->getEntityManager()->getConnection();
        return $conn->prepare($sql)
            ->executeQuery([
                'popularity' => $popularity,
                'date' => (new \DateTime('- 5 years'))->format('Y-m-d'),
                'vote' => $vote
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

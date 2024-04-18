<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    // Requete avec QueryBuilder
    public function findBySeveralCriterias(string $genre1, string $genre2, string $date = null): array
    {
        $q = $this->createQueryBuilder('s')
            ->orderBy('s.firstAirDate', 'DESC')
            ->andWhere('s.genres = :genre1 OR s.genres = :genre2')
            ->setParameter(':genre1', $genre1)
            ->setParameter(':genre2', $genre2);

        if ($date) {
            $q->andWhere('s.firstAirDate > :date')
                ->setParameter(':date', $date);
        }

        return $q->getQuery()
            ->getResult();
    }

    // requete avec DQL
    public function findWithDql(): array
    {
        $query = "SELECT s FROM App\Entity\Serie AS s 
                    WHERE (s.genres = :genre1 OR s.genres = :genre2) 
                    AND s.firstAirDate > :date ORDER BY s.firstAirDate DESC";

        return $this->getEntityManager()->createQuery($query)
            ->setParameter(':genre1', 'SF')
            ->setParameter(':genre2', 'Comedy')
            ->setParameter(':date', (new \DateTime('- 3 year'))->format('Y-m-d'))
            ->execute();
    }

    // Requete EN SQL brut
    public function findWithSql(): array
    {
        $sql = <<<SQL
            SELECT * FROM serie 
            WHERE (genres = :genre1 OR genres = :genre2) 
              AND first_air_date > :date 
            ORDER BY first_air_date DESC
SQL;

        $connexion = $this->getEntityManager()->getConnection();

        return $connexion->prepare($sql)
            ->executeQuery([':genre1' => 'SF', ':genre2' => 'Comedy', ':date' => '2019-04-15'])
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

<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ParameterBagInterface $parameterBag)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findSeriesWithQueryBuilder(int $offset, int $nbParPage, string $genre = "", bool $count = false): array
    {
        //$nbParPage = $this->parameterBag->get('serie')['nb_par_page'];

        $q = $this->createQueryBuilder('s')
            ->andWhere('s.status = :status OR s.firstAirDate >= :date')
            ->setParameter('status', 'returning')
            ->setParameter('date', new \DateTime('1998-01-01'));

        if ($genre) {
            $q->andWhere('s.genres like :genre')
                ->setParameter('genre', "%$genre%");
        }

        if (!$count) {
            return $q->orderBy('s.popularity', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($nbParPage)
                ->getQuery()
                ->getResult();
        }

        return $q->select('count(s.id)')->getQuery()->getOneOrNullResult();

    }

    public function findSeriesWithDQL(int $offset, int $nbParPage, string $genre): array
    {

        $dql = <<<DQL
            SELECT s FROM App\Entity\Serie s
            WHERE (s.status = :status OR s.firstAirDate >= :date)
            AND s.genres like :genre
            ORDER BY s.popularity DESC
DQL;

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter('status', 'returning')
            ->setParameter('date', new \DateTime('1998-01-01'))
            ->setParameter('genre', "%$genre%")
            ->setMaxResults($nbParPage)
            ->setFirstResult($offset)
            ->execute();
    }

    /**
     * @throws Exception
     */
    public function getSeriesWithRawSQL(int $offset, int $nbParPage): array
    {
        $sql = <<<SQL
            SELECT * FROM serie WHERE genres like :genre
            ORDER BY popularity DESC
            LIMIT $nbParPage OFFSET $offset                    
SQL;

        $conn = $this->getEntityManager()->getConnection();
        return $conn->prepare($sql)
            ->executeQuery([
                'genre' => '%Drama%'
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

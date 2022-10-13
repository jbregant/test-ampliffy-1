<?php

namespace App\Repository;

use App\Entity\LocalGitRepo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocalGitRepo>
 *
 * @method LocalGitRepo|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocalGitRepo|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocalGitRepo[]    findAll()
 * @method LocalGitRepo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalGitRepoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalGitRepo::class);
    }

    public function add(LocalGitRepo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LocalGitRepo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LocalGitRepo[] Returns an array of LocalGitRepo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LocalGitRepo
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

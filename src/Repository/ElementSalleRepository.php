<?php

namespace App\Repository;

use App\Entity\ElementSalle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ElementSalle>
 *
 * @method ElementSalle|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElementSalle|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElementSalle[]    findAll()
 * @method ElementSalle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementSalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElementSalle::class);
    }

    public function add(ElementSalle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ElementSalle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getElement($id){
        return $this->createQueryBuilder('e')
            ->andWhere('e.salle = :id')
           ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return ElementSalle[] Returns an array of ElementSalle objects
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

//    public function findOneBySomeField($value): ?ElementSalle
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function save(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // @TODO j'arrive pas à avoir le vendor dans le résultat, sauf quand je mets le groupe customer mais 
    // ça rend le vendor autant de fois que de résultat
    public function findAllWithPagination($page, $limit, Vendor $vendor)
    {
      $queryBuilder = $this->createQueryBuilder('c')
        ->select('c', 'v')
        ->leftJoin('c.vendor', 'v')
        ->andWhere('v = :vendor')
        ->setParameter('vendor', $vendor)
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
      ;

      return $queryBuilder->getQuery()->getResult();
    }

    public function findCustomersByVendor($page, $limit, Vendor $vendor)
    {
      $queryBuilder = $this->createQueryBuilder('c')
        ->select('c', 'v')
        ->leftJoin('c.vendor', 'v')
        ->andWhere('v = :vendor')
        ->setParameter('vendor', $vendor)
      ;

      return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return Customer[] Returns an array of Customer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Customer
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

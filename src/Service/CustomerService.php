<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vendor;
use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CustomerService implements CustomerServiceInterface
{
  private $dateTimeImmutable;

  public function __construct(
    private EntityManagerInterface $entityManager,
    private TagAwareCacheInterface $cache
  )
  {
    $this->dateTimeImmutable = new DateTimeImmutable();
  }

  public function create(Customer $customer, Vendor $vendor): ?Customer
  {
    $date = $this->dateTimeImmutable;
    // @TODO gestion erreur : Faudra aussi gérer le cas où l'email existe déjà
    $customer
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
      ->setVendor($vendor)
    ;
    $this->entityManager->persist($customer);
    $this->entityManager->flush();
    
    return $customer;
  }

  public function delete(Customer $customer)
  {
    $this->cache->invalidateTags(['getCustomersByVendor', 'getCustomerByVendor']);
    $this->entityManager->remove($customer);
    $this->entityManager->flush();
    // @TODO Peut-être retourner une réponse ici
  }
}

<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vendor;
use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class CustomerService implements CustomerServiceInterface
{
  private $dateTimeImmutable;

  public function __construct(
    private EntityManagerInterface $entityManager
  )
  {
    $this->dateTimeImmutable = new DateTimeImmutable();
  }

  public function create(Customer $customer, int $vendorId): ?Customer
  {
    $date = $this->dateTimeImmutable;
    // @TODO modifier quand j'aurai mis en place le JWT pour éviter de faire cet appel à la BDD inutile
    // Faudra aussi gérer le cas où l'email existe déjà
    $vendor = $this->entityManager->getRepository(Vendor::class)->find($vendorId);
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
    $this->entityManager->remove($customer);
    $this->entityManager->flush();
    // @TODO Peut-être retourner une réponse ici
  }
}

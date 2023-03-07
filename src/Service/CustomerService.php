<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vendor;
use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class CustomerService implements CustomerServiceInterface
{
  private $dateTimeImmutable;

  public function __construct(
    private EntityManagerInterface $entityManager,
    private TagAwareCacheInterface $cache,
    private SerializerInterface $serializer
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

  public function delete(Vendor $vendor, Customer $customer): JsonResponse
  {
    if($vendor !== $customer->getVendor()){
      $jsonErrorMessage = $this->serializer->serialize(['message' => 'NOT AUTHORIZED'], 'json');

      return new JsonResponse($jsonErrorMessage, Response::HTTP_FORBIDDEN, [], true);
    }

    $this->cache->invalidateTags(['getCustomersByVendor', 'getCustomerByVendor']);
    $this->entityManager->remove($customer);
    $this->entityManager->flush();
    $jsonErrorMessage = $this->serializer->serialize(['message' => 'Utilisateur supprimé'], 'json');

    return new JsonResponse($jsonErrorMessage, Response::HTTP_OK, [], true);
  }
}

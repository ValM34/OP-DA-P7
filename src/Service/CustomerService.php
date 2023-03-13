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
use Symfony\Contracts\Cache\ItemInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;

class CustomerService implements CustomerServiceInterface
{
  private $dateTimeImmutable;

  public function __construct(
    private EntityManagerInterface $entityManager,
    private TagAwareCacheInterface $cache,
    private SerializerInterface $serializer,
    private TagAwareCacheInterface $cachePool
  )
  {
    $this->dateTimeImmutable = new DateTimeImmutable();
  }

  public function getCustomer(Customer $customer): string
  {
    $idCache = 'getCustomerByVendor-' . $customer->getId();
    $jsonCustomer = $this->cachePool->get($idCache, function (ItemInterface $item) use ($customer) {
      $item->tag("getCustomerByVendor");
      $context = SerializationContext::create()->setGroups(['customers']);
      
      return $this->serializer->serialize($customer, 'json', $context);
    });

    return $jsonCustomer;
  }

  public function create(Request $request, Vendor $vendor): string
  {
    $customer = $this->serializer->deserialize($request->getContent(), Customer::class, 'json');
    $date = $this->dateTimeImmutable;
    $customer
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
      ->setVendor($vendor)
    ;
    $this->entityManager->persist($customer);
    $this->entityManager->flush();
    $context = SerializationContext::create()->setGroups(['customer']);
    $jsonCustomer = $this->serializer->serialize($customer, 'json', $context);
    
    return $jsonCustomer;
  }

  public function delete(Vendor $vendor, Customer $customer): JsonResponse
  {
    if($vendor !== $customer->getVendor()){
      $jsonErrorMessage = $this->serializer->serialize(['message' => 'Non autorisé'], 'json');

      return new JsonResponse($jsonErrorMessage, Response::HTTP_FORBIDDEN, [], true);
    }

    $this->cache->invalidateTags(['getCustomersByVendor', 'getCustomerByVendor']);
    $this->entityManager->remove($customer);
    $this->entityManager->flush();
    $jsonErrorMessage = $this->serializer->serialize(['message' => 'Utilisateur supprimé'], 'json');

    return new JsonResponse($jsonErrorMessage, Response::HTTP_OK, [], true);
  }
}

<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use App\Entity\Vendor;

class VendorService implements VendorServiceInterface
{
  public function __construct(
    private TagAwareCacheInterface $cachePool,
    private CustomerRepository $customerRepository,
    private SerializerInterface $serializer,
    private SerializationContext $serializationContext
  )
  {}

  public function getCustomersByVendor(
    Vendor $vendor,
    Request $request
  ): string
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);
    $idCache = "getCustomersByVendor-" . $page . "-" . $limit;
    $jsonCustomers = $this->cachePool->get($idCache, function (ItemInterface $item) use ($page, $limit, $vendor) {
      $item->tag("getCustomersByVendor");
      $context = SerializationContext::create()->setGroups(['customers']);
      $customerList = $this->customerRepository->findAllWithPagination($page, $limit, $vendor);
      
      return $this->serializer->serialize($customerList, 'json', $context);
    });

    return $jsonCustomers;
  }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Customer;
use App\Entity\Vendor;
use App\Repository\CustomerRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use App\Service\CustomerServiceInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CustomerController extends AbstractController
{
  public function __construct(
    private CustomerRepository $customerRepository,
    private SerializerInterface $serializer,
    private CustomerServiceInterface $customerService
  )
  {}
  
  // GET CUSTOMERS BY VENDOR
  #[Route('/api/vendor', name: 'app_customer_get_all', methods: ['GET'])]
  public function getCustomersByVendor(Request $request, TagAwareCacheInterface $cachePool, CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);
    $idCache = "getCustomersByVendor-" . $page . "-" . $limit;
    $jsonCustomers = $cachePool->get($idCache, function (ItemInterface $item) use ($customerRepository, $page, $limit, $serializer) {
      $item->tag("getCustomersByVendor");
      $context = (new ObjectNormalizerContextBuilder())
        ->withGroups('customers')
        ->toArray()
      ;
      $customerList = $this->customerRepository->findAllWithPagination($page, $limit, $this->getUser());
      
      return $serializer->serialize($customerList, 'json', $context);
    });

    return new JsonResponse($jsonCustomers, Response::HTTP_OK, [], true);
  }

  // GET CUSTOMER BY VENDOR
  #[Route('/api/customer/{id}', name: 'app_customer_get_one', methods: ['GET'])]
  public function getCustomerByVendor(Request $request, Customer $customer, TagAwareCacheInterface $cachePool, SerializerInterface $serializer)
  {
    $idCache = 'getCustomerByVendor-' . $customer->getId();
    $jsonCustomer = $cachePool->get($idCache, function (ItemInterface $item) use ($customer, $serializer) {
      $item->tag("getCustomerByVendor");
      $context = (new ObjectNormalizerContextBuilder())
        ->withGroups('customers')
        ->toArray()
      ;
      
      return $serializer->serialize($customer, 'json', $context);
    });
    

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  // CREATE
  #[Route('/api/customer/add', name: 'app_customer_add', methods: ['POST'])]
  public function create(Customer $customer, Request $request)
  {
    $customer = $this->serializer->deserialize($request->getContent(), Customer::class, 'json');
    $customer = $this->customerService->create($customer, $this->getUser());
    $context = (new ObjectNormalizerContextBuilder())
      ->withGroups('customer')
      ->toArray()
    ;
    $jsonCustomer = $this->serializer->serialize($customer, 'json', $context);

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  // DELETE
  #[Route('/api/customer/delete/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
  public function delete(Customer $customer)
  {
    $this->customerService->delete($customer);

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}

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

class CustomerController extends AbstractController
{
  public function __construct(
    private CustomerRepository $customerRepository,
    private SerializerInterface $serializer,
    private CustomerServiceInterface $customerService
  )
  {}
  
  // GET CUSTOMERS BY VENDOR
  #[Route('/api/vendor/{id}', name: 'app_customer_get_all', methods: ['GET'])]
  public function getCustomersByVendor(Vendor $vendor): JsonResponse
  {
    $context = (new ObjectNormalizerContextBuilder())
      ->withGroups('customers')
      ->toArray()
    ;
    $jsonCustomers = $this->serializer->serialize($vendor, 'json', $context);

    return new JsonResponse($jsonCustomers, Response::HTTP_OK, [], true);
  }

  // GET CUSTOMER BY VENDOR
  #[Route('/api/customer/{id}', name: 'app_customer_get_one', methods: ['GET'])]
  public function getCustomerByVendor(Customer $customer)
  {
    $context = (new ObjectNormalizerContextBuilder())
      ->withGroups('customer')
      ->toArray()
    ;
    $jsonCustomer = $this->serializer->serialize($customer, 'json', $context);

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  // CREATE
  #[Route('/api/customer/add/{id}', name: 'app_customer_add', methods: ['POST'])]
  public function add(Customer $customer, Request $request, int $id)
  {
    $customer = $this->serializer->deserialize($request->getContent(), Customer::class, 'json');
    $customer = $this->customerService->create($customer, $id);
    dd($customer);
  }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Customer;
use App\Entity\Vendor;
use App\Repository\CustomerRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class CustomerController extends AbstractController
{
  public function __construct(
    private CustomerRepository $customerRepository,
    private SerializerInterface $serializer
  )
  {}
  
  // GET ALL
  #[Route('/api/vendor/{id}', name: 'app_customer_get_all')]
  public function getCustomersByVendor(Vendor $vendor): JsonResponse
  {
    $context = (new ObjectNormalizerContextBuilder())
      ->withGroups('customers')
      ->toArray()
    ;
    $jsonCustomers = $this->serializer->serialize($vendor, 'json', $context);

    return new JsonResponse($jsonCustomers, Response::HTTP_OK, [], true);
  }
}

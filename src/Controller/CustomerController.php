<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Entity\Vendor;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use App\Service\CustomerServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Form\CustomerType;

class CustomerController extends AbstractController
{
  public function __construct(
    private CustomerRepository $customerRepository,
    private SerializerInterface $serializer,
    private CustomerServiceInterface $customerService
  ) {
  }

  /**
   * GET ALL BY VENDOR
   */
  #[OA\Response(
    response: 200,
    description: "Retourne la liste des clients liés à un vendeur",
    content: new OA\JsonContent(ref: new Model(type: Vendor::class, groups: ['customers']))
  )]
  #[OA\Parameter(
    name: 'page',
    in: 'query',
    description: "La page que l'on veut récupérer",
    schema: new OA\Schema(type: 'int')
  )]
  #[OA\Parameter(
    name: 'limit',
    in: 'query',
    description: "Le nombre d'éléments que l'on veut récupérer",
    schema: new OA\Schema(type: 'int')
  )]
  #[OA\Tag(name: "Customer")]
  #[Route('/api/customer/all', name: 'app_customer_get_all', methods: ['GET'])]
  public function getCustomersByVendor(Request $request): JsonResponse
  {
    $jsonCustomers = $this->customerService->getCustomersByVendor($this->getUser(), $request);

    return new JsonResponse($jsonCustomers, Response::HTTP_OK, [], true);
  }

  /**
   * GET ONE
   */
  #[OA\Response(
    response: 200,
    description: "Retourne un client lié à un vendeur en fonction de l'identifiant du client.",
    content: new OA\JsonContent(ref: new Model(type: Customer::class, groups: ['customers']))
  )]
  #[OA\Tag(name: "Customer")]
  #[Route('/api/customer/{id}', name: 'app_customer_get_one', methods: ['GET'])]
  public function getCustomer(Customer $customer)
  {
    // check for "view" access: calls all voters
    $this->denyAccessUnlessGranted('view', $customer);

    $jsonCustomer = $this->customerService->getCustomer($customer);

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  /**
   * CREATE
   */
  #[OA\RequestBody(
    content: new OA\JsonContent(
      ref: new Model(type: Customer::class, groups: ['nelmioCreateCustomer'])
    )
  )]
  #[OA\Response(
    response: 200,
    description: "Retourne le client nouvellement créé.",
    content: new OA\JsonContent(ref: new Model(type: Customer::class, groups: ['customers']))
  )]
  #[OA\Tag(name: "Customer")]
  #[Route('/api/customer/add', name: 'app_customer_add', methods: ['POST'])]
  public function create(Request $request)
  {
    $customer = new Customer();
    $form = $this->createForm(CustomerType::class, $customer, [
      'csrf_protection' => false,
    ]);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);

    if ($form->isSubmitted() && $form->isValid()) {
      $jsonCustomer = $this->customerService->create($request, $this->getUser(), $customer);

      return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }

    $jsonErrorMessage = $this->serializer->serialize(['message' => 'Requête invalide'], 'json');

    return new JsonResponse($jsonErrorMessage, Response::HTTP_FORBIDDEN, [], true);
  }

  /**
   * DELETE
   */
  #[OA\Response(
    response: 200,
    description: "Retourne un tableau vide.",
    content: new OA\JsonContent(ref: new Model(type: Customer::class))
  )]
  #[OA\Tag(name: "Customer")]
  #[Route('/api/customer/delete/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
  public function delete(Customer $customer)
  {
    return $this->customerService->delete($this->getUser(), $customer);
  }
}

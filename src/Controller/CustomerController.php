<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use App\Service\CustomerServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Service\VendorServiceInterface;

class CustomerController extends AbstractController
{
  public function __construct(
    private CustomerRepository $customerRepository,
    private SerializerInterface $serializer,
    private CustomerServiceInterface $customerService,
    private VendorServiceInterface $vendorService
  )
  {}

  /**
  * Cette méthode permet de récupérer les clients liés à un vendeur.
  *
  * @OA\Response(
  *     response=200,
  *     description="Retourne un client lié à un vendeur en fonction de l'identifiant du client.",
  *     @OA\JsonContent(
  *        type="array",
  *        @OA\Items(ref=@Model(type=Customer::class, groups={"customers"}))
  *     )
  * )
  * @OA\Tag(name="Customer")
  *
  * @param ProductRepository $productRepository
  * @param SerializerInterface $serializer
  * @param Request $request
  * @return JsonResponse
  */
  // GET CUSTOMER BY VENDOR
  #[Route('/api/customer/{id}', name: 'app_customer_get_one', methods: ['GET'])]
  public function getCustomer(Customer $customer)
  {
    if($this->getUser() !== $customer->getVendor()){
      $jsonErrorMessage = $this->serializer->serialize(['message' => 'Non autorisé'], 'json');

      return new JsonResponse($jsonErrorMessage, Response::HTTP_FORBIDDEN, [], true);
    }
    
    $jsonCustomer = $this->customerService->getCustomer($customer);

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  // @TODO : Voir si je dois faire quelque chose de spécial sur cette route car ça va créer un utilisateur.
  /**
  * Cette méthode permet d'ajouter un client lié à un vendeur.
  *
  * @OA\Response(
  *     response=200,
  *     description="Retourne le client nouvellement créé.",
  *     @OA\JsonContent(
  *        type="array",
  *        @OA\Items(ref=@Model(type=Customer::class, groups={"customers"}))
  *     )
  * )
  * @OA\Tag(name="Customer")
  *
  * @param ProductRepository $productRepository
  * @param SerializerInterface $serializer
  * @param Request $request
  * @return JsonResponse
  */
  // CREATE
  #[Route('/api/customer/add', name: 'app_customer_add', methods: ['POST'])]
  public function create(Request $request)
  {
    $jsonCustomer = $this->customerService->create($request, $this->getUser());

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  /**
  * Cette méthode permet de supprimer un client lié à un vendeur.
  *
  * @OA\Response(
  *     response=200,
  *     description="Retourne le client nouvellement créé.",
  *     @OA\JsonContent(
  *        type="array",
  *        @OA\Items(ref=@Model(type=Customer::class))
  *     )
  * )
  * @OA\Tag(name="Customer")
  *
  * @param ProductRepository $productRepository
  * @param SerializerInterface $serializer
  * @param Request $request
  * @return JsonResponse
  */
  // DELETE
  #[Route('/api/customer/delete/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
  public function delete(Customer $customer)
  {
    return $this->customerService->delete($this->getUser(), $customer);
  }
}

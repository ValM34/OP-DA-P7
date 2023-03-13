<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use App\Service\CustomerServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Service\VendorServiceInterface;

class VendorController extends AbstractController
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
  *     description="Retourne la liste des clients liés à un vendeur",
  *     @OA\JsonContent(
  *        type="array",
  *        @OA\Items(ref=@Model(type=Vendor::class, groups={"customers"}))
  *     )
  * )
  * @OA\Parameter(
  *     name="page",
  *     in="query",
  *     description="La page que l'on veut récupérer",
  *     @OA\Schema(type="int")
  * )
  *
  * @OA\Parameter(
  *     name="limit",
  *     in="query",
  *     description="Le nombre d'éléments que l'on veut récupérer",
  *     @OA\Schema(type="int")
  * )
  * @OA\Tag(name="Customer")
  *
  * @param ProductRepository $productRepository
  * @param SerializerInterface $serializer
  * @param Request $request
  * @return JsonResponse
  */
  // GET CUSTOMERS BY VENDOR
  #[Route('/api/vendor', name: 'app_customer_get_all', methods: ['GET'])]
  public function getCustomersByVendor(Request $request): JsonResponse
  {
    $jsonCustomers = $this->vendorService->getCustomersByVendor($this->getUser(), $request);

    return new JsonResponse($jsonCustomers, Response::HTTP_OK, [], true);
  }
}

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
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use App\Service\CustomerServiceInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class CustomerController extends AbstractController
{
  public function __construct(
    private CustomerRepository $customerRepository,
    private SerializerInterface $serializer,
    private CustomerServiceInterface $customerService
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
  public function getCustomersByVendor(Request $request, TagAwareCacheInterface $cachePool, CustomerRepository $customerRepository, SerializerInterface $serializer, SerializationContext $serializationContext): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);
    $idCache = "getCustomersByVendor-" . $page . "-" . $limit;
    $jsonCustomers = $cachePool->get($idCache, function (ItemInterface $item) use ($customerRepository, $page, $limit, $serializer) {
      $item->tag("getCustomersByVendor");
      $context = SerializationContext::create()->setGroups(['customers']);
      $customerList = $this->customerRepository->findAllWithPagination($page, $limit, $this->getUser());
      
      return $serializer->serialize($customerList, 'json', $context);
    });

    return new JsonResponse($jsonCustomers, Response::HTTP_OK, [], true);
  }

  // @TODO : n'importe quel vendeur peut voir n'importe quel client, ce n'est pas souhaitable, il faut le modifier
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
  public function getCustomerByVendor(Request $request, Customer $customer, TagAwareCacheInterface $cachePool, SerializerInterface $serializer, SerializationContext $serializationContext)
  {
    $idCache = 'getCustomerByVendor-' . $customer->getId();
    $jsonCustomer = $cachePool->get($idCache, function (ItemInterface $item) use ($customer, $serializer) {
      $item->tag("getCustomerByVendor");
      $context = SerializationContext::create()->setGroups(['customers']);
      
      return $serializer->serialize($customer, 'json', $context);
    });
    

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
  public function create(Customer $customer, Request $request, SerializationContext $serializationContext)
  {
    $customer = $this->serializer->deserialize($request->getContent(), Customer::class, 'json');
    $customer = $this->customerService->create($customer, $this->getUser());
    $context = SerializationContext::create()->setGroups(['customer']);
    $jsonCustomer = $this->serializer->serialize($customer, 'json', $context);

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  // @TODO : Je pense pas que j'ai vérifié si le client est lié au vendeur, ce qui est problématique  car un client non lié
  // au vendeur pourrait être supprimé par un autre vendeur.
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
  // @TODO : Voir avec Laurent s'il faut ajouter de l'auto découvrabilité sur cette route
  // DELETE
  #[Route('/api/customer/delete/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
  public function delete(Customer $customer)
  {
    $this->customerService->delete($customer);

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}

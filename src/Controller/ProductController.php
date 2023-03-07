<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use App\Controller\Trait\statusSetterTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends AbstractController
{
  public function __construct(
    private ProductRepository $productRepository,
    private SerializerInterface $serializer
    )
  {}

  /**
  * Cette méthode permet de récupérer l'ensemble des produits.
  *
  * @OA\Response(
  *     response=200,
  *     description="Retourne la liste des produits",
  *     @OA\JsonContent(
  *        type="array",
  *        @OA\Items(ref=@Model(type=Product::class, groups={"products"}))
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
  * @OA\Tag(name="Product")
  *
  * @param ProductRepository $productRepository
  * @param SerializerInterface $serializer
  * @param Request $request
  * @return JsonResponse
  */
  // GET ALL
  #[Route('/api/product/all', name: 'app_product_get_all', methods: 'GET')]
  public function getAll(Request $request, SerializerInterface $serializer, TagAwareCacheInterface $cachePool, ProductRepository $productRepository, SerializationContext $serializationContext): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);
    $idCache = "getAllProducts-" . $page . "-" . $limit;
    $jsonProductList = $cachePool->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit, $serializer) {
      $item->tag("getAllProducts");
      $context = SerializationContext::create()->setGroups(['products']);
      $productList = $productRepository->findAllWithPagination($page, $limit, $this->getUser());

      return $serializer->serialize($productList, 'json', $context);
    });

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }

  /**
  * Cette méthode permet de récupérer un produit en renseignant son id.
  *
  * @OA\Response(
  *     response=200,
  *     description="Retourne un produit dont l'id en renseigné en paramètre de l'url.",
  *     @OA\JsonContent(
  *        type="array",
  *        @OA\Items(ref=@Model(type=Product::class, groups={"product"}))
  *     )
  * )
  *
  * @OA\Tag(name="Product")
  *
  * @param ProductRepository $productRepository
  * @param SerializerInterface $serializer
  * @param Request $request
  * @return JsonResponse
  */
  // GET ONE
  #[Route('/api/product/{id}', name: 'app_product_get_one', methods: 'GET')]
  public function getOne(Product $product, SerializerInterface $serializer, TagAwareCacheInterface $cachePool, SerializationContext $serializationContext): JsonResponse
  {
    $idCache = 'getOneProduct-' . $product->getId();
    $jsonProduct = $cachePool->get($idCache, function (ItemInterface $item) use ($product, $serializer) {
      $item->tag("getOneProduct");
      $context = SerializationContext::create()->setGroups(['product']);
      dd('sefsdf');
      if(!$product){
        throw new NotFoundHttpException('La ressource demandée n\'a pas été trouvée.');
      }
      
      return $serializer->serialize($product, 'json', $context);
    });

    return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
  }
}

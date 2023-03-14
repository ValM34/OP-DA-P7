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
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Service\ProductServiceInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProductController extends AbstractController
{
  public function __construct(
    private ProductRepository $productRepository,
    private SerializerInterface $serializer,
    private ProductServiceInterface $productService
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
  public function getAll(Request $request): JsonResponse
  {
    $jsonProductList = $this->productService->getAll($request, $this->getUser());

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
  public function getOne(Product $product): JsonResponse
  {
    $jsonProduct = $this->productService->getOne($product);

    return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
  }

  #[Route('/api/test', name: 'app_test', methods: 'GET')]
  public function test(Product $product, PaginatorInterface $paginator, Request $request)
  {
    $productList = $this->productRepository->findAll();

    $pagination = $paginator->paginate(
      $productList,
      $request->query->getInt('page', 1),
      $request->query->getInt('limit', 10)
    );

    $jsonProductList = $this->serializer->serialize($pagination, 'json');

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }
}

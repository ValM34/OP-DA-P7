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
use OpenApi\Attributes as OA;
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
   * GET ALL
   */
  #[OA\Response(
    response: 200,
    description: "Retourne la liste des produits",
    content: new OA\JsonContent(ref: new Model(type: Product::class, groups: ['products']))
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
  #[OA\Tag(name: 'Product')]
  #[Route('/api/product/all', name: 'app_product_get_all', methods: 'GET')]
  public function getAll(Request $request): JsonResponse
  {
    $jsonProductList = $this->productService->getAll($request, $this->getUser());

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }

  /**
   * GET ONE
   */
  #[OA\Response(
    response: 200, 
    description: "Retourne un produit dont l'id est renseigné en paramètre de l'url.",
    content: new OA\JsonContent(ref: new Model(type: Product::class, groups: ['product']))
  )]
  #[OA\Tag(name: 'Product')]
  #[Route('/api/product/{id}', name: 'app_product_get_one', methods: 'GET')]
  public function getOne(Product $product): JsonResponse
  {
    $jsonProduct = $this->productService->getOne($product);

    return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
  }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductController extends AbstractController
{
  public function __construct(
    private ProductRepository $productRepository,
    private SerializerInterface $serializer
    )
  {}
  
  // GET ALL
  #[Route('/api/product/all', name: 'app_product_get_all', methods: 'GET')]
  public function getAll(Request $request, SerializerInterface $serializer, TagAwareCacheInterface $cachePool, ProductRepository $productRepository): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);
    $idCache = "getAllProducts-" . $page . "-" . $limit;
    $jsonProductList = $cachePool->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit, $serializer) {
      $item->tag("getAllProducts");
      $customerList = $productRepository->findAllWithPagination($page, $limit, $this->getUser());
      
      return $serializer->serialize($customerList, 'json');
    });

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }

  // GET ONE
  #[Route('/api/product/{id}', name: 'app_product_get_one', methods: 'GET')]
  public function getOne(Product $product, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
  {
    $idCache = 'getOneCustomer-' . $product->getId();
    $jsonProduct = $cachePool->get($idCache, function (ItemInterface $item) use ($product, $serializer) {
      $item->tag("getOneCustomer");
      
      return $serializer->serialize($product, 'json');
    });

    return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
  }
}

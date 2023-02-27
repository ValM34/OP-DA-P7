<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
  public function __construct(
    private ProductRepository $productRepository,
    private SerializerInterface $serializer
    )
  {}
  
  // GET ALL
  #[Route('/api/product/all', name: 'app_product_get_all', methods: 'GET')]
  public function getAll(): JsonResponse
  {
    $jsonProductList = $this->serializer->serialize($this->productRepository->findAll(), 'json');

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }

  // GET ONE
  #[Route('/api/product/{id}', name: 'app_product_get_one', methods: 'GET')]
  public function getOne(Product $product): JsonResponse
  {
    $jsonProduct = $this->serializer->serialize($product, 'json');

    return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
  }
}

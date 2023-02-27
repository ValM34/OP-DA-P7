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
  #[Route('/api/product/all', name: 'app_product_all')]
  public function getAll(): JsonResponse
  {
    $jsonProductList = $this->serializer->serialize($this->productRepository->findAll(), 'json');

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }
}

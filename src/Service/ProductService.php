<?php

namespace App\Service;

use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use App\Repository\ProductRepository;
use App\Entity\Vendor;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class ProductService implements ProductServiceInterface
{

  public function __construct(
    private TagAwareCacheInterface $cachePool,
    private SerializerInterface $serializer,
    private ProductRepository $productRepository,
    private PaginatorInterface $paginator
  )
  {}

  public function getAll(Request $request, Vendor $vendor): string
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);
    $idCache = "getAllProducts-" . $page . "-" . $limit;
    $jsonProductList = $this->cachePool->get($idCache, function (ItemInterface $item) use ($page, $limit, $vendor) {
      $item->tag("getAllProducts");
      $context = SerializationContext::create()->setGroups([
        'Default',
        'items' => ['products']
      ]);
      $productList = $this->productRepository->findAll();
      $productListPaginated = $this->paginator->paginate($productList, $page, $limit);

      return $this->serializer->serialize($productListPaginated, 'json', $context);
    });

    return $jsonProductList;
  }

  public function getOne(Product $product): string
  {
    $idCache = 'getOneProduct-' . $product->getId();
    $jsonProduct = $this->cachePool->get($idCache, function (ItemInterface $item) use ($product) {
      $item->tag("getOneProduct");
      $context = SerializationContext::create()->setGroups(['product']);
      
      return $this->serializer->serialize($product, 'json', $context);
    });

    return $jsonProduct;
  }
}

<?php

namespace App\Service;

use App\Entity\Vendor;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;

interface ProductServiceInterface
{
  public function getAll(Request $request, Vendor $vendor): string;
  public function getOne(Product $product): string;
}

<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vendor;
use Symfony\Component\HttpFoundation\JsonResponse;

interface CustomerServiceInterface
{
  public function create(Customer $customer, Vendor $vendor): ?Customer;
  public function delete(Vendor $vendor, Customer $customer): JsonResponse;
}

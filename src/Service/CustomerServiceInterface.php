<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vendor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface CustomerServiceInterface
{
  public function getCustomersByVendor(Vendor $vendor, Request $request): string;
  public function getCustomer(Customer $customer): string;
  public function create(Request $request, Vendor $vendor, Customer $customer): string;
  public function delete(Vendor $vendor, Customer $customer): JsonResponse;
}

<?php

namespace App\Service;

use App\Entity\Customer;

interface CustomerServiceInterface
{
  public function create(Customer $customer, int $vendorId): ?Customer;
}

<?php

namespace App\Service;

use App\Entity\Customer;

interface CustomerServiceInterface
{
  public function create(Customer $customer, int $vendorId): ?Customer;
  public function delete(Customer $customer); // @TODO voir ce que je mets en return value
}

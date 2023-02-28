<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vendor;

interface CustomerServiceInterface
{
  public function create(Customer $customer, Vendor $vendor): ?Customer;
  public function delete(Customer $customer); // @TODO voir ce que je mets en return value
}

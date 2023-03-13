<?php

namespace App\Service;

use App\Entity\Vendor;
use Symfony\Component\HttpFoundation\Request;

interface VendorServiceInterface
{
  public function getCustomersByVendor(Vendor $vendor, Request $request): string;
}

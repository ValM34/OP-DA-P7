<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;
use App\Entity\Product;
use App\Entity\Vendor;
use App\Entity\Customer;

class AppFixtures extends Fixture
{
  private $dateTimeImmutable;
  private $product;

  public function __construct()
  {
    $this->dateTimeImmutable = new DateTimeImmutable();
  }

  public function load(ObjectManager $manager): void
  {
    $date = $this->dateTimeImmutable;
    for($i = 0; $i < 50; $i++){
      $product = new Product();
      $product
        ->setName('product name' . $i)
        ->setDescription('product description' . $i)
        ->setPrice(2000)
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
      ;
      $manager->persist($product);
    }

    for($i = 0; $i < 10; $i++){
      $vendor = new Vendor();
      $vendor
        ->setName('vendeur' . + $i)
        ->setEmail('email@' . $i . '.com')
        ->setPassword('password')
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
      ;
      $manager->persist($vendor);
      $listVendor[] = $vendor;
    }

    for($i = 0; $i < 30; $i++){
      $customer = new Customer();
      $customer
        ->setName('name' . $i)
        ->setSurname('surname' . $i)
        ->setEmail('emailcustomer@' . $i . '.com')
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
        ->setVendor($listVendor[array_rand($listVendor)]);
      ;
      $manager->persist($customer);
    }
    $manager->flush();
  }
}

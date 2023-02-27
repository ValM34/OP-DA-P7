<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;
use App\Entity\Product;

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
    $manager->flush();
  }
}

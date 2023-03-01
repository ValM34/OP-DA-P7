<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;

 /**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_product_get_all",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="product")
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_product_get_one",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="products")
 * )
 */

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['product', 'products'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['product', 'products'])]
  private ?string $name = null;

  #[ORM\Column(length: 255)]
  #[Groups(['product', 'products'])]
  private ?string $description = null;

  #[ORM\Column]
  #[Groups(['product', 'products'])]
  private ?int $price = null;

  #[ORM\Column]
  #[Groups(['product', 'products'])]
  private ?\DateTimeImmutable $updatedAt = null;

  #[ORM\Column]
  #[Groups(['product', 'products'])]
  private ?\DateTimeImmutable $createdAt = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getPrice(): ?int
  {
    return $this->price;
  }

  public function setPrice(int $price): self
  {
    $this->price = $price;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
      return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
  {
      $this->updatedAt = $updatedAt;

      return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
      return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): self
  {
      $this->createdAt = $createdAt;

      return $this;
  }
}

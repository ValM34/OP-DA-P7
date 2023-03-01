<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;

 /**
 * @Hateoas\Relation(
 *      "customer",
 *      href = @Hateoas\Route(
 *          "app_customer_get_one",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="customers")
 * ) 
 * @Hateoas\Relation(
 *      "vendor",
 *      href = @Hateoas\Route(
 *          "app_customer_get_all",
 *          parameters = { "id" = "expr(object.getVendor().getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="customers")
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_customer_get_one",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="customer")
 * ) 
 * @Hateoas\Relation(
 *      "vendor",
 *      href = @Hateoas\Route(
 *          "app_customer_get_all",
 *          parameters = { "id" = "expr(object.getVendor().getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="customer")
 * )
 */

 // @TODO : Les relations vers "vendor" sont potentiellement inutiles car un client n'est pas sensé avec accès
// aux infos des utilisateurs des autres clients. Voir avec Laurent.

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['customers', 'customer'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['customers', 'customer'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['customers', 'customer'])]
    private ?string $surname = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['customers', 'customer'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['customers', 'customer'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    #[Groups(['customers', 'customer'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['customer'])]
    private ?Vendor $vendor = null;

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    public function setVendor(?Vendor $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }
}

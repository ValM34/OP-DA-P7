<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

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

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['customers', 'customer'])]
    private ?int $id = null;

    #[ORM\Column(length: 150, type: 'string')]
    #[Assert\Length(min: 3, max: 150, minMessage: 'Votre prénom doit contenir au moins {{ limit }} caractères', maxMessage: 'Votre prénom ne doit pas dépasser {{ limit }} caractères')]
    #[Groups(['customers', 'customer', 'nelmioCreateCustomer'])]
    private ?string $name = null;

    #[ORM\Column(length: 150, type: 'string')]
    #[Assert\Length(min: 3, max: 150, minMessage: 'Votre nom doit contenir au moins {{ limit }} caractères', maxMessage: 'Votre nom ne doit pas dépasser {{ limit }} caractères')]
    #[Groups(['customers', 'customer', 'nelmioCreateCustomer'])]
    private ?string $surname = null;

    #[ORM\Column(length: 180, unique: true, type: 'string')]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Assert\Length(max: 180, maxMessage: 'Votre email doit contenir au moins {{ limit }} caractères')]
    #[Groups(['customers', 'customer', 'nelmioCreateCustomer'])]
    private ?string $email = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['customers', 'customer'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
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

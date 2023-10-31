<?php

namespace App\Entity;

use App\Entity\Product;
use App\Entity\PanierItem;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    const DEVISE = 'eur';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeToken = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brandStripe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $last4Stripe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idChargeStripe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statusStripe = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Product $product = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Panier $panier = null;




    public function __construct()
    {
    }






    public function getId(): ?int
    {
        return $this->id;
    }



    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStripeToken(): ?string
    {
        return $this->stripeToken;
    }

    /**
     * @param null|string $stripeToken
     */
    public function setStripeToken(?string $stripeToken): static
    {
        $this->stripeToken = $stripeToken;

        return $this;
    }

    /**
     * @param null|string $brandStripe
     */
    public function getBrandStripe(): ?string
    {
        return $this->brandStripe;
    }

    public function setBrandStripe(?string $brandStripe): static
    {
        $this->brandStripe = $brandStripe;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLast4Stripe(): ?string
    {
        return $this->last4Stripe;
    }

    public function setLast4Stripe(?string $last4Stripe): static
    {
        $this->last4Stripe = $last4Stripe;

        return $this;
    }

    /**
     * @param null|string $idChargeStripe
     */
    public function getIdChargeStripe(): ?string
    {
        return $this->idChargeStripe;
    }

    public function setIdChargeStripe(?string $idChargeStripe): static
    {
        $this->idChargeStripe = $idChargeStripe;

        return $this;
    }


    public function getStatusStripe(): ?string
    {
        return $this->statusStripe;
    }

    public function setStatusStripe(?string $statusStripe): static
    {
        $this->statusStripe = $statusStripe;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function addProduct(Product $product)
    {
        $this->product[] = $product;
        $product->addOrder($this);
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;

        return $this;
    }
}

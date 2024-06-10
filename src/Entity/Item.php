<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Item
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $entityId;

    #[ORM\Column(type: "string", length: 255)]
    private string $categoryName;

    #[ORM\Column(type: "string", length: 255)]
    private string $sku;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "array", nullable: true)]
    private ?array $description;

    #[ORM\Column(type: "string", length: 255)]
    private string $shortdesc;

    #[ORM\Column(type: "decimal", scale: 4)]
    private float $price;

    #[ORM\Column(type: "string", length: 255)]
    private string $link;

    #[ORM\Column(type: "string", length: 255)]
    private string $image;

    #[ORM\Column(type: "string", length: 255)]
    private string $brand;

    #[ORM\Column(type: "integer")]
    private int $rating;

    #[ORM\Column(type: "string", length: 255)]
    private string $caffeineType;

    #[ORM\Column(type: "integer")]
    private int $count;

    #[ORM\Column(type: "boolean")]
    private bool $flavored;

    #[ORM\Column(type: "boolean")]
    private bool $seasonal;

    #[ORM\Column(type: "boolean")]
    private bool $instock;

    #[ORM\Column(type: "boolean")]
    private bool $facebook;

    #[ORM\Column(type: "boolean")]
    private bool $isKCup;

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): Item
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): Item
    {
        $this->categoryName = $categoryName;
        return $this;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): Item
    {
        $this->sku = $sku;
        return $this;
    }

    public function getDescription(): array|null
    {
        return $this->description;
    }

    public function setDescription(array|null $description): Item
    {
        $this->description = $description;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Item
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): Item
    {
        $this->price = $price;
        return $this;
    }

    public function getShortdesc(): string
    {
        return $this->shortdesc;
    }

    public function setShortdesc(string $shortdesc): Item
    {
        $this->shortdesc = $shortdesc;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): Item
    {
        $this->link = $link;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): Item
    {
        $this->image = $image;
        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): Item
    {
        $this->brand = $brand;
        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): Item
    {
        $this->rating = $rating;
        return $this;
    }

    public function getCaffeineType(): string
    {
        return $this->caffeineType;
    }

    public function setCaffeineType(string $caffeineType): Item
    {
        $this->caffeineType = $caffeineType;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): Item
    {
        $this->count = $count;
        return $this;
    }

    public function getFlavored(): bool
    {
        return $this->flavored;
    }

    public function setFlavored(bool $flavored): Item
    {
        $this->flavored = $flavored;
        return $this;
    }

    public function getInstock(): bool
    {
        return $this->instock;
    }

    public function setInstock(bool $instock): Item
    {
        $this->instock = $instock;
        return $this;
    }

    public function getSeasonal(): bool
    {
        return $this->seasonal;
    }

    public function setSeasonal(bool $seasonal): Item
    {
        $this->seasonal = $seasonal;
        return $this;
    }

    public function getFacebook(): bool
    {
        return $this->facebook;
    }

    public function setFacebook(bool $facebook): Item
    {
        $this->facebook = $facebook;
        return $this;
    }

    public function getIsKCup(): bool
    {
        return $this->isKCup;
    }

    public function setIsKCup(bool $isKCup): Item
    {
        $this->isKCup = $isKCup;
        return $this;
    }
}

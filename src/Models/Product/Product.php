<?php

namespace App\Models\Product;

abstract class Product
{
    protected int $id;
    protected string $name;
    protected float $price;
    protected string $imageUrl;
    protected bool $inStock;

    public function __construct($id, $name, $price, $imageUrl, $inStock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->imageUrl = $imageUrl;
        $this->inStock = $inStock;
    }

    abstract public function getType(): string;

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getImageUrl(): string { return $this->imageUrl; }
    public function isInStock(): bool { return $this->inStock; }
}

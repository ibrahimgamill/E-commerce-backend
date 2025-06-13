<?php

namespace App\Models\Product;

class ClothingProduct extends Product
{
    public function getType(): string
    {
        return 'clothing';
    }
}

<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Product',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
                'inStock' => Type::boolean(),
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => function ($root) {
                        // fetch gallery images from DB, or return $root['gallery'] if eager loaded
                        return $root['gallery'] ?? [];
                    }
                ],
                'description' => Type::string(),
                'category' => Type::string(),
                'brand' => Type::string(),
                'attributes' => [
                    'type' => Type::listOf(new AttributeSetType()),
                    'resolve' => function ($root) {
                        // fetch attributes from DB, or return $root['attributes'] if eager loaded
                        return $root['attributes'] ?? [];
                    }
                ],
                'prices' => [
                    'type' => Type::listOf(new PriceType()),
                    'resolve' => function ($root) {
                        // fetch prices from DB, or return $root['prices'] if eager loaded
                        return $root['prices'] ?? [];
                    }
                ],
            ]
        ]);
    }
}

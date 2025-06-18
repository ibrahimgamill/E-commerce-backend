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
            'fields' => function () {
                return [
                    'id' => Type::nonNull(Type::string()),
                    'name' => Type::string(),
                    'inStock' => Type::boolean(),
                    'gallery' => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => function ($root) {
                            return $root['gallery'] ?? [];
                        }
                    ],
                    'description' => Type::string(),
                    'category' => Type::string(),
                    'brand' => Type::string(),
                    'attributes' => [
                        'type' => Type::listOf(Types::attributeSet()),
                        'resolve' => function ($root) {
                            return $root['attributes'] ?? [];
                        }
                    ],
                    'prices' => [
                        'type' => Type::listOf(Types::price()),
                        'resolve' => function ($root) {
                            return $root['prices'] ?? [];
                        }
                    ],
                ];
            }
        ]);
    }
}

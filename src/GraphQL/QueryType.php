<?php

namespace App\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Types; // <-- use the new Types class
use App\Services\ProductService;

class QueryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'   => 'Query',
            'fields' => [
                'products' => [
                    'type'    => Type::listOf(Types::product()),
                    'args'    => [
                        'category' => [
                            'type' => Type::string(),
                            'description' => 'The category to filter products by',
                        ]
                    ],
                    'resolve' => function($root, $args) {
                        $service = new ProductService();
                        if (isset($args['category'])) {
                            return $service->getProductsByCategory($args['category']);
                        }
                        return $service->getAllProducts();
                    }
                ],
                'categories' => [
                    'type'    => Type::listOf(Types::category()),
                    'resolve' => function() {
                        $service = new ProductService();
                        return $service->getCategories();
                    }
                ],
                'product' => [
                    'type'    => Types::product(),
                    'args'    => [
                        'id' => Type::nonNull(Type::string())
                    ],
                    'resolve' => function($root, $args) {
                        $service = new ProductService();
                        return $service->getProductById($args['id']);
                    }
                ]
            ]
        ]);
    }
}

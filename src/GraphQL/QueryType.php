<?php

namespace App\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\ProductType;
use App\GraphQL\Types\CategoryType;
use App\Services\ProductService;

class QueryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'   => 'Query',
            'fields' => [
                'products' => [
                    'type'    => Type::listOf(new ProductType()),
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
                    'type'    => Type::listOf(new CategoryType()),
                    'resolve' => function() {
                        $service = new ProductService();
                        return $service->getCategories();
                    }
                ],
                'product' => [
                    'type'    => new ProductType(),
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

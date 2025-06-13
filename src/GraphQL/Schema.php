<?php

namespace App\GraphQL;

use GraphQL\Type\Schema as GraphQLSchema;

class Schema
{
    public static function build(): GraphQLSchema
    {
        return new GraphQLSchema([
            'query' => new QueryType()
        ]);
    }
}

<?php

namespace App\GraphQL\Args;

use Overblog\GraphQLBundle\Definition\Builder\MappingInterface;

class PaginatoringBuilder implements MappingInterface
{
    public function toMappingDefinition(array $config)
    {
        $defaultLimit = isset($config['defaultLimit']) ? (int)$config['defaultLimit'] : 20;

        return [
            'limit' => [
                'type' => 'Int!',
                'defaultValue' => $defaultLimit,
            ],
            'offset' => [
                'type' => 'Int!',
                'defaultValue' => 0,
            ],
            'locale' => [
                'type' => 'String',
                'defaultValue' => 'ru',
            ],
        ];
    }
}
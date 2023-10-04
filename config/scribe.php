<?php

use Knuckles\Scribe\Extracting\Strategies;

return [

    'theme' => 'default',
    'title' => null,
    'description' => '',
    'base_url' => null,
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains' => ['*'],
                'versions' => ['v1'],
            ],

            'include' => [
                // 'users.index', 'healthcheck*'
            ],

            'exclude' => [
                // '/health', 'admin.*'
            ],

            'apply' => [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'response_calls' => [
                    'methods' => ['GET'],
                    'config' => [
                        'app.env' => 'documentation',
                        // 'app.debug' => false,
                    ],
                    'queryParams' => [
                        // 'key' => 'value',
                    ],

                    'bodyParams' => [
                        // 'key' => 'value',
                    ],

                    'fileParams' => [
                        // 'key' => 'storage/app/image.png',
                    ],

                    'cookies' => [
                        // 'name' => 'value'
                    ],
                ],
            ],
        ],
    ],
    'type' => 'static',
    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes' => true,

        'docs_url' => '/docs',
        'assets_directory' => null,
        'middleware' => [],
    ],

    'try_it_out' => [
        'enabled' => true,
        'base_url' => null,
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    'auth' => [
        'enabled' => false,
        'default' => false,
        'in' => 'bearer',
        'name' => 'key',
        'use_value' => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{YOUR_AUTH_KEY}',
        'extra_info' => 'You can retrieve your token by visiting your dashboard and clicking <b>Generate API token</b>.',
    ],

    'intro_text' => <<<INTRO
This documentation aims to provide all the information you need to work with our API.

<aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>
INTRO
    ,

    'example_languages' => [
        'bash',
        'javascript',
    ],

    'postman' => [
        'enabled' => true,
        'overrides' => [
            // 'info.version' => '2.0.0',
        ],
    ],

    'openapi' => [
        'enabled' => true,

        'overrides' => [
            // 'info.version' => '2.0.0',
        ],
    ],

    'groups' => [

        'default' => 'Endpoints',

        'order' => [
            // 'This group will come first',
            // 'This group will come next' => [
            //     'POST /this-endpoint-will-comes-first',
            //     'GET /this-endpoint-will-comes-next',
            // ],
            // 'This group will come third' => [
            //     'This subgroup will come first' => [
            //         'GET /this-other-endpoint-will-comes-first',
            //         'GET /this-other-endpoint-will-comes-next',
            //     ]
            // ]
        ],
    ],

    'logo' => false,

    'last_updated' => 'Last updated: {date:F j, Y}',

    'examples' => [
        'faker_seed' => null,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [
            Strategies\Metadata\GetFromDocBlocks::class,
            Strategies\Metadata\GetFromMetadataAttributes::class,
        ],
        'urlParameters' => [
            Strategies\UrlParameters\GetFromLaravelAPI::class,
            Strategies\UrlParameters\GetFromLumenAPI::class,
            Strategies\UrlParameters\GetFromUrlParamAttribute::class,
            Strategies\UrlParameters\GetFromUrlParamTag::class,
        ],
        'queryParameters' => [
            Strategies\QueryParameters\GetFromFormRequest::class,
            Strategies\QueryParameters\GetFromInlineValidator::class,
            Strategies\QueryParameters\GetFromQueryParamAttribute::class,
            Strategies\QueryParameters\GetFromQueryParamTag::class,
        ],
        'headers' => [
            Strategies\Headers\GetFromRouteRules::class,
            Strategies\Headers\GetFromHeaderAttribute::class,
            Strategies\Headers\GetFromHeaderTag::class,
        ],
        'bodyParameters' => [
            Strategies\BodyParameters\GetFromFormRequest::class,
            Strategies\BodyParameters\GetFromInlineValidator::class,
            Strategies\BodyParameters\GetFromBodyParamAttribute::class,
            Strategies\BodyParameters\GetFromBodyParamTag::class,
        ],
        'responses' => [
            Strategies\Responses\UseResponseAttributes::class,
            Strategies\Responses\UseTransformerTags::class,
            Strategies\Responses\UseApiResourceTags::class,
            Strategies\Responses\UseResponseTag::class,
            Strategies\Responses\UseResponseFileTag::class,
            Strategies\Responses\ResponseCalls::class,
        ],
        'responseFields' => [
            Strategies\ResponseFields\GetFromResponseFieldAttribute::class,
            Strategies\ResponseFields\GetFromResponseFieldTag::class,
        ],
    ],

    'fractal' => [
        'serializer' => null,
    ],

    'routeMatcher' => \Knuckles\Scribe\Matching\RouteMatcher::class,

    'database_connections_to_transact' => [config('database.default')]
];

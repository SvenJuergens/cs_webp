<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "cs_webp"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Convert images to webp',
    'description' => 'Store png and jpg images in webp format too (only fileadmin/_processed_ folder).',
    'category' => 'plugin',
    'author' => 'Angela Dudtkowski, Sven Juergens',
    'author_email' => 't3YYYY@blue-side.de',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

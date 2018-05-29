<?php
return [
    [
        'question' => 'Foo?',
        'answers' => [
            [
                'value' => 'foo',
                'correct' => false
            ],
            [
                'value' => 'bar',
                'correct' => false
            ],
            [
                'value' => 'baz',
                'correct' => true
            ],
            [
                'value' => 'bazinga',
                'correct' => false
            ],
        ],
        'category' => 'A'
    ],
    [
        'question' => 'Bar?',
        'versions' => [
            'lib/a' => '^1.1|~2.0',
            'php' => '^5.3|~7.0',
        ],
        'answers' => [
            [
                'value' => 'foo',
                'correct' => true
            ],
            [
                'value' => 'bar',
                'correct' => false
            ],
            [
                'value' => 'baz',
                'correct' => false
            ],
            [
                'value' => 'bazinga',
                'correct' => false
            ],
        ],
        'category' => 'A'
    ],
    [
        'question' => 'Baz?',
        'versions' => [
            'lib/a' => '~2.0',
        ],
        'answers' => [
            [
                'value' => 'foo',
                'correct' => false
            ],
            [
                'value' => 'bar',
                'correct' => false
            ],
            [
                'value' => 'baz',
                'correct' => true
            ],
            [
                'value' => 'bazinga',
                'correct' => false
            ],
        ],
        'category' => 'A'
    ],
];

<?php

declare(strict_types=1);

return [
    'JokeAvatar' => [
        'size' => 200,
        'themes' => [
            'default' => [
                'background_colors' => ['#E8F4F8', '#F0E6F6', '#E6F0FA', '#FFF3E0', '#F3E5F5'],
                'text_color' => '#2C3E50',
                'decorative_colors' => ['#BBDEFB', '#C8E6C9', '#FFE0B2', '#E1BEE7', '#B2EBF2'],
                'decorative_min' => 3,
                'decorative_max' => 8,
                'decorative_size_min' => 10,
                'decorative_size_max' => 30,
            ],
            'dark' => [
                'background_colors' => ['#2C3E50', '#34495E', '#1A252F', '#212F3D'],
                'text_color' => '#ECF0F1',
                'decorative_colors' => ['#5D6D7E', '#7F8C8D', '#85929E', '#AEB6BF'],
                'decorative_min' => 2,
                'decorative_max' => 6,
                'decorative_size_min' => 10,
                'decorative_size_max' => 25,
            ],
        ],
    ],
];
<?php
Kirby::plugin('samrm/icons-field', [
    'fields' => [
        'icons' => [
            'props' => [
                'categories' => function ($categories = []) {
                    return $categories;
                },
            ],
            'computed' => [
                'options' => function () {
                    $data = Data::read(__DIR__ . "/src/assets/Fork-Awesome/src/icons/icons.yml");
                    return $this->formatIconsData($data['icons']);
                },
                'default' => function () {
                    return $this->default;
                },
                'value' => function () {
                    return $this->value ?? '';
                },
                'search' => function (bool $search = true) {
                    return $search;
                },
            ],
            'methods' => [
                'formatIconsData' => function ($icons) {
                    $options = array();
                    foreach ($icons as $icon) {
                        if (array_key_exists('categories', $icon) && $this->hasIntersections($icon['categories'], $this->categories)) {
                            $options[] = [
                                'text' => $icon['name'],
                                'value' => $icon['id'],
                                'alias' => array_key_exists('filter', $icon) ? $icon['filter'] : [],
                                'categories' => array_key_exists('categories', $icon) ? $icon['categories'] : []
                            ];
                        }
                    }
                    return $options;
                },
                'hasIntersections' => function ($array1, $array2) {
                    if (count($array2)) {
                        $intersections = array_intersect($array1, $array2);
                        return count($intersections) >= 1;
                    } else {
                        return true;
                    }
                }
            ]
        ]
    ]
]);

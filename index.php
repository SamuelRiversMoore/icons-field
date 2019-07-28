<?php
Kirby::plugin('samrm/icons-field', [
    'options' => [
        'cache' => true
    ],
    'fields' => [
        'icons' => [
            'props' => [
                'categories' => function ($categories = []) {
                    return $categories;
                },
            ],
            'computed' => [
                'options' => function () {
                    return $this->getIconsData();
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
                'getIconsData' => function () {
                    $cache = kirby()->cache('samrm.icons-field');
                    $icons  = $cache->get('icons');
                    if ($icons == null) {
                        $data = Data::read(__DIR__ . "/src/assets/icons.yml");
                        $icons = $this->formatDataAsOptions($data['icons']);
                        $cache->set('icons', $icons);
                    }
                    return $icons;
                },
                'formatDataAsOptions' => function ($icons) {
                    $options = array();
                    foreach ($icons as $icon) {
                        if (array_key_exists('categories', $icon) && $this->hasIntersections($icon['categories'], $this->categories)) {
                            $filters = array_key_exists('filter', $icon) && is_array($icon['filter']) ? $icon['filter'] : [];
                            $aliases = array_key_exists('aliases', $icon) && is_array($icon['aliases']) ? $icon['aliases'] : [];
                            $options[] = [
                                'text' => $icon['name'],
                                'value' => $icon['id'],
                                'aliases' => implode(" ", array_merge($aliases, $filters)),
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

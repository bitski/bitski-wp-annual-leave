<?php

/**
 * Returns an array of substitute data.
 */
function praxis_eichholz_substitute_data(): array
{
    $praxis_eichholz_substitute_data = [];
    $post_type = 'substitute';

    $args = [
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ];

    $query = new WP_Query($args);
    $posts = $query->posts;
    foreach ($posts as $post) {
        $post_ID = $post->ID;
        $title   = get_the_title($post_ID);
        $address = get_field('address', $post_ID);
        $phone   = get_field('phone', $post_ID);

        $praxis_eichholz_substitute_data[$post_ID] = [
            'title'   => $title,
            'address' => $address,
            'phone'   => $phone
        ];
    }

    return $praxis_eichholz_substitute_data;
}

<?php

/**
 * Returns an array of annual leave data.
 */
function praxis_eichholz_annual_leave_data(): array
{
    /**
     * Post ID of the post that contains the ACF fields for the annual leave data,
     * 52 is the ID of the page 'Unsere Praxis'.
     * 4 is the default number of annual leave entries.
     */
    $post_id            = 52;
    $annual_leave_count = 4;
    $annual_leave_data  = [];

    for ($i = 1; $i <= $annual_leave_count; $i++) {
        $group_name  = 'annual_leave_' . $i;
        $group_field = get_field($group_name, $post_id);

        if (
            empty($group_field) ||
            empty($group_field['period']['start']) ||
            empty($group_field['period']['end'])
        ) {
            continue;
        }

        $substitutes = [];

        for ($j = 1; $j <= 4; $j++) {
            $substitute_group_name  = 'substitute_' . $j;
            $substitute_group_field = $group_field[$substitute_group_name] ?? [];

            if (
                empty($substitute_group_field) ||
                empty($substitute_group_field['substitute_id'])
            ) {
                continue;
            }

            $substitutes[] = [
                'substitute_id' => $substitute_group_field['substitute_id'],
                'period'        => $substitute_group_field['period'],
            ];
        }

        $annual_leave_data[] = [
            'name'        => $group_name,
            'title'       => 'Annual leave ' . $i,
            'dates'       => [
                'start' => $group_field['period']['start'],
                'end'   => $group_field['period']['end'],
            ],
            'substitutes' => $substitutes,
        ];
    }

    usort($annual_leave_data, static function ($a, $b) {
        return strtotime($a['dates']['start']) <=>
            strtotime($b['dates']['start']);
    });

    return $annual_leave_data;
}

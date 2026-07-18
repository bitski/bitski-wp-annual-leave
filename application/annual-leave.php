<?php

/**
 * Annual leave module.
 *
 * ACF-based annual leave rendering (accordion and callout).
 */

/**
 * Renders annual leave information for the appropriate accordion item.
 *
 * @throws DateMalformedStringException
 */
function praxis_eichholz_annual_leave_render_accordion(): string
{
    $annual_leave_accordion_data = praxis_eichholz_annual_leave_accordion_data();

    if ($annual_leave_accordion_data === []) {
        return '';
    }

    ob_start(); ?>
    <h4><strong>Unsere Urlaubszeiten</strong></h4>
    <ul>
        <?php
        foreach ($annual_leave_accordion_data as $data) {
            $start_object = new DateTimeImmutable($data['dates']['start']);
            $end_object   = new DateTimeImmutable($data['dates']['end']); ?>
            <li>
                <time datetime="<?php
                echo esc_attr($start_object->format('Y-m-d')); ?>"><?php
                    echo esc_html($start_object->format('d.m.Y')); ?></time>
                &ndash;
                <time datetime="<?php
                echo esc_attr($end_object->format('Y-m-d')); ?>">
                    <?php
                    echo esc_html($end_object->format('d.m.Y')); ?></time>
            </li>
            <?php
        } ?>
    </ul>
    <?php
    return ob_get_clean();
}

/**
 * Conditionally, renders annual leave information for the callout.
 *
 * Renders the callout only if the current date is within the range of any of the annual leave dates.
 */
function praxis_eichholz_annual_leave_render_callout(): string
{
    $current_annual_leave_data = praxis_eichholz_annual_leave_callout_data();

    ob_start();
    $start_object = new DateTimeImmutable($current_annual_leave_data['dates']['start']);
    $end_object   = new DateTimeImmutable($current_annual_leave_data['dates']['end']); ?>
    <br>
    <p><strong>Die Praxis ist im Moment geschlossen, da wir gerade im Urlaub
            sind:</strong></p>
    <p>
        <time datetime="<?php
        echo esc_attr($start_object->format('Y-m-d')); ?>">
            <?php
            echo esc_html($start_object->format('d.m.Y')); ?></time>
        &ndash;
        <time datetime="<?php
        echo esc_attr($end_object->format('Y-m-d')); ?>">
            <?php
            echo esc_html($end_object->format('d.m.Y')); ?>
        </time>
    </p>
    <p>
        <?php


        ?>
    </p>
    <?php
    return ob_get_clean();
}

function praxis_eichholz_annual_leave_accordion_data(): array
{
    $annual_leave_data = praxis_eichholz_annual_leave_data();
    $annual_leave_accordion_data = $annual_leave_data;

    usort($annual_leave_accordion_data, static function ($a, $b) {
        return strtotime($a['dates']['start']) <=>
            strtotime($b['dates']['start']);
    });

    foreach ($annual_leave_accordion_data as $key => $data) {
        // Removes entries with invalid date periods.
        $start_object = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $data['dates']['start'],
        );
        $end_object   = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $data['dates']['end'],
        );

        if (
            ! ($start_object instanceof DateTimeImmutable) ||
            ! ($end_object instanceof DateTimeImmutable)
        ) {
            unset($annual_leave_accordion_data[$key]);
        }
    }

    return $annual_leave_accordion_data;
}

/**
 *
 */
function praxis_eichholz_annual_leave_callout_data(): array
{
    $annual_leave_data = praxis_eichholz_annual_leave_data();
    $annual_leave_callout_data = [];

    $current_date = date('Y-m-d');

    foreach ($annual_leave_data as $data) {
        // Skips entries with invalid date periods.
        $start_object = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $data['dates']['start'],
        );
        $end_object   = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $data['dates']['end'],
        );

        if (
            ! ($start_object instanceof DateTimeImmutable) ||
            ! ($end_object instanceof DateTimeImmutable)
        ) {
            continue;
        }

        if (
            $current_date >= $data['dates']['start'] &&
            $current_date <= $data['dates']['end']
        ) {
            $annual_leave_callout_data = $data;
            break;
        }
    }

    if ($annual_leave_callout_data !== []) {
        // TODO: Add substitute information.;
    }

    return $annual_leave_callout_data;
}


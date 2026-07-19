<?php

/**
 * Annual leave module.
 *
 * ACF-based annual leave rendering (accordion and callout).
 */

// Renderers

/**
 * Renders annual leave information for the appropriate accordion item.
 *
 * @throws DateMalformedStringException
 */
function praxis_eichholz_annual_leave_render_accordion(): string
{
    $annual_leave_accordion_view_model = praxis_eichholz_annual_leave_accordion_view_model(
    );

    if ($annual_leave_accordion_view_model === []) {
        return '';
    }

    ob_start(); ?>
    <h4><strong>Unsere Urlaubszeiten</strong></h4>
    <ul>
        <?php
        foreach ($annual_leave_accordion_view_model as $data) {
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
    $current_annual_leave_data = praxis_eichholz_annual_leave_callout_view_model();

    ob_start();
    $start_object = new DateTimeImmutable(
            $current_annual_leave_data['dates']['start'],
    );
    $end_object   = new DateTimeImmutable(
            $current_annual_leave_data['dates']['end'],
    ); ?>
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

// View models

/**
 * Creates the view model for the annual leave accordion.
 */
function praxis_eichholz_annual_leave_accordion_view_model(): array
{
    $annual_leave_data           = praxis_eichholz_annual_leave_data();
    $annual_leave_accordion_view_model = $annual_leave_data;

    usort($annual_leave_accordion_view_model, static function ($a, $b) {
        return $a['dates']['start'] <=>
                $b['dates']['start'];
    });

    foreach ($annual_leave_accordion_view_model as $key => $data) {
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
                ! ($start_object instanceof DateTimeImmutable)
                ||
                ! ($end_object instanceof DateTimeImmutable)
        ) {
            unset($annual_leave_accordion_view_model[$key]);
        }
    }

    return $annual_leave_accordion_view_model;
}

/**
 * Creates the view model for the annual leave callout.
 */
function praxis_eichholz_annual_leave_callout_view_model(): array
{
    $annual_leave_data         = praxis_eichholz_annual_leave_data();
    $annual_leave_callout_view_model = [];

    $current_date_object = new DateTimeImmutable('today');

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
                ! ($start_object instanceof DateTimeImmutable)
                ||
                ! ($end_object instanceof DateTimeImmutable)
        ) {
            continue;
        }

        if (
                $current_date_object >= $start_object
                && $current_date_object <= $end_object
        ) {
            $annual_leave_callout_view_model = $data;
            break;
        }
    }

    if (
            $annual_leave_callout_view_model !== []
            && ! empty($annual_leave_callout_view_model['substitutes'])
    ) {
        $substitute_data = praxis_eichholz_substitute_data();
        foreach (
                $annual_leave_callout_view_model['substitutes'] as $index =>
                $substitute
        ) {
            $substitute_id = $substitute['substitute_id'];

            if (!isset($substitute_data[$substitute_id])) {
                continue;
            }

            $annual_leave_callout_view_model['substitutes'][$index] = array_merge(
                    $substitute,
                    $substitute_data[$substitute_id],
            );
        }
    }

    return $annual_leave_callout_view_model;
}

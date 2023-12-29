<?php

namespace Storage\Storage\Core;

class Form
{
    protected const FIELDS = [];

    private static function get_initial_value(
        string $fld_name,
        array $fld_params,
        array $initial = [],
    ): string {
        if (isset($initial[$fld_name])) {
            $val = $initial[$fld_name];
        } else if (isset($fld_params['initial'])) {
            $val = $fld_params['initial'];
        } else {
            $val = '';
        }
        return $val;
    }

    protected static function after_initialize_data(array &$data): void
    {
    }

    public static function get_initial_data(array $initial = []): array
    {
        $data = [];
        foreach (static::FIELDS as $fld_name => $fld_params) {
            $data[$fld_name] = self::get_initial_value(
                $fld_name,
                $fld_params,
                $initial,
            );
        }
        static::after_initialize_data($data);
        return $data;
    }

    protected static function after_normalize_data(array &$data, array &$errors, array &$results): void
    {
    }

    public static function get_normalized_data(array $form_data = null): array
    {
        $data = [];
        $errors = [];
        $results = [];
        foreach (static::FIELDS as $fld_name => $fld_params) {
            $fld_type = (isset($fld_params['type'])) ?
                $fld_params['type'] : 'string';
            if ($fld_type == 'boolean') {
                $data[$fld_name] = !empty($form_data[$fld_name]);
            } else {
                if (empty($form_data[$fld_name])) {
                    $data[$fld_name] = self::get_initial_value($fld_name, $fld_params);
                    if (!isset($fld_params['optional'])) {
                        $errors[$fld_name] = 'Заполните поле';
                    }
                } else {
                    $fld_value = $form_data[$fld_name];
                    switch ($fld_type) {
                        case 'integer':
                            $v = filter_var(
                                $fld_value,
                                FILTER_SANITIZE_NUMBER_INT,
                            );
                            if ($v) {
                                $data[$fld_name] = $v;
                            } else {
                                $errors[$fld_name] = 'Введите целое число';
                            }
                            break;
                        case 'float':
                            $v = filter_var(
                                $fld_value,
                                FILTER_SANITIZE_NUMBER_FLOAT,
                                ['flags' => FILTER_FLAG_ALLOW_FRACTION],
                            );
                            if ($v) {
                                $data[$fld_name] = $v;
                            } else {
                                $errors[$fld_name] = 'Введите вещественное число';
                            }
                            break;
                        case 'email':
                            $v = filter_var(
                                $fld_value,
                                FILTER_SANITIZE_EMAIL,
                            );
                            if ($v) {
                                $data[$fld_name] = $v;
                            } else {
                                $errors[$fld_name] = 'Введите адрес электронной почты';
                            }
                            break;
                        default:
                            $data[$fld_name] = filter_var(
                                $fld_value,
                                FILTER_SANITIZE_STRING,
                            );
                    }
                }
            }
        }

        if (!$errors) {
            static::after_normalize_data($data, $errors, $results);
        }

        if ($errors) {
            $data['__errors'] = $errors;
        }
        if ($results) {
            $data['__results'] = $results;
        }
        return $data;
    }

    protected static function after_prepare_data(array &$data, array &$norm_data): void
    {
    }

    public static function get_prepared_data(array $norm_data): array
    {
        $data = [];
        foreach (static::FIELDS as $fld_name => $fld_params) {
            if (
                !isset($fld_params['nosave']) &&
                isset($norm_data[$fld_name])
            ) {
                $data[$fld_name] = $norm_data[$fld_name];
            }
        }
        static::after_prepare_data($data, $norm_data);
        return $data;
    }
}

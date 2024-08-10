<?php

namespace Solmer\Storage\Core;

class Form
{
    protected const FIELDS = [];

    private static function getInitialValue(
        string $fldName,
        array $fldParams,
        array $initial = [],
    ): string {
        if (isset($initial[$fldName])) {
            $val = $initial[$fldName];
        } elseif (isset($fldParams['initial'])) {
            $val = $fldParams['initial'];
        } else {
            $val = '';
        }
        return $val;
    }

    protected static function afterInitializeData(array &$data): void
    {
    }

    public static function getInitialData(array $initial = []): array
    {
        $data = [];
        foreach (static::FIELDS as $fldName => $fldParams) {
            $data[$fldName] = self::getInitialValue(
                $fldName,
                $fldParams,
                $initial,
            );
        }
        static::afterInitializeData($data);
        return $data;
    }

    protected static function afterNormalizeData(
        array &$data,
        array &$errors,
        array &$results,
    ): void {
    }

    public static function getNormalizedData(array $formData = null): array
    {
        $data = [];
        $errors = [];
        $results = [];
        foreach (static::FIELDS as $fldName => $fldParams) {
            $fldType = (isset($fldParams['type'])) ?
                $fldParams['type'] : 'string';
            if ($fldType == 'boolean') {
                $data[$fldName] = !empty($formData[$fldName]);
            } else {
                if (empty($formData[$fldName])) {
                    $data[$fldName] = self::getInitialValue($fldName, $fldParams);
                    if (!isset($fldParams['optional'])) {
                        $errors[$fldName] = 'Заполните поле';
                    }
                } else {
                    $fldValue = $formData[$fldName];
                    switch ($fldType) {
                        case 'integer':
                            $v = filter_var(
                                $fldValue,
                                FILTER_SANITIZE_NUMBER_INT,
                            );
                            if ($v) {
                                $data[$fldName] = $v;
                            } else {
                                $errors[$fldName] = 'Введите целое число';
                            }
                            break;
                        case 'float':
                            $v = filter_var(
                                $fldValue,
                                FILTER_SANITIZE_NUMBER_FLOAT,
                                ['flags' => FILTER_FLAG_ALLOW_FRACTION],
                            );
                            if ($v) {
                                $data[$fldName] = $v;
                            } else {
                                $errors[$fldName] = 'Введите вещественное число';
                            }
                            break;
                        case 'email':
                            $v = filter_var(
                                $fldValue,
                                FILTER_SANITIZE_EMAIL,
                            );
                            if ($v) {
                                $data[$fldName] = $v;
                            } else {
                                $errors[$fldName] = 'Введите адрес электронной почты';
                            }
                            break;
                        default:
                            $data[$fldName] = filter_var(
                                $fldValue,
                                FILTER_SANITIZE_STRING,
                            );
                            break;
                    }
                }
            }
        }

        if (!$errors) {
            static::afterNormalizeData($data, $errors, $results);
        }

        if ($errors) {
            $data['__errors'] = $errors;
        }
        if ($results) {
            $data['__results'] = $results;
        }
        return $data;
    }

    protected static function afterPrepareData(array &$data, array &$normData): void
    {
    }

    public static function getPreparedData(array $normData): array
    {
        $data = [];
        foreach (static::FIELDS as $fldName => $fldParams) {
            if (
                !isset($fldParams['nosave']) &&
                isset($normData[$fldName])
            ) {
                $data[$fldName] = $normData[$fldName];
            }
        }
        static::afterPrepareData($data, $normData);
        return $data;
    }
}

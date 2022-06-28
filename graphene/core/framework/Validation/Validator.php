<?php

namespace Graphene\Validation;

class Validator
{

    private array $fields = [];
    private array $errors = [];

    private array $errorMessages = [
        'required_field_is_not_filled' => 'Поле <strong>#name#</strong> обязательно к заполнению!',
        'min_length_error' => 'Длинна поля <strong>#name#</strong> меньше чем требуется. Мин: <strong>#minLength#</strong> симв.',
        'max_length_error' => 'Длинна поля <strong>#name#</strong> больше чем требуется. Макс: <strong>#maxLength#</strong> симв.',
    ];

    public function __construct()
    {

    }

    public function setErrorMessage($code, $message): void
    {
        $this->errorMessages[$code] = trim($message);
    }

    private function getErrorMessage($code, array $vars = []): string
    {
        return strtr($this->errorMessages[$code], $vars);
    }

    public function validate($params)
    {

        if (!$this->fields) {
            return false;
        }

        $this->fields = $this->sanitize($this->fields, $params);


        foreach ($this->fields as $field) {

            $manifest = $field['manifest'];

            if ($manifest['required'] and !$field['value']) {
                $this->setError($field, 'required_field_is_not_filled');
            }

            if ($field['value'] and $manifest['length']['min'] and $manifest['length']['min'] > $field['length']) {
                $this->setError($field, 'min_length_error');
            }

            if ($field['value'] and $manifest['length']['max'] and $manifest['length']['max'] < $field['length']) {
                $this->setError($field, 'max_length_error');
            }

            if ($manifest['regExp']) {
                foreach ($manifest['regExp'] as $code => $regExp) {
                    if ($regExp and $field['value']) {
                        $regExp = rtrim($regExp, '/');
                        $regExp = ltrim($regExp, '/');
                        if (!preg_match("/$regExp/", $field['value'])) {
                            $this->setError($field, $code);
                        }
                    }
                }
            }

            if ($manifest['handler'] and $field['value']) {

                $handler = $manifest['handler'];

                $errorCode = $handler($field['value']);

                if ($errorCode) {
                    $this->setError($field, $errorCode);
                }

            }

        }

        $errors = $this->getErrors();

        return (object)[
            'errors' => $errors
        ];
    }


    private function setError($field, $errorCode): void
    {

        if (!$field['manifest']['name']) {
            $field['manifest']['name'] = $field['code'];
        }

        $message = $this->getErrorMessage($errorCode, [
            '#name#' => $field['manifest']['name'],
            '#value#' => $field['value'],
            '#minLength#' => $field['manifest']['length']['min'],
            '#maxLength#' => $field['manifest']['length']['max']
        ]);

        $this->errors[] = [
            'code' => $errorCode,
            'message' => $message,
            'field' => [
                'name' => $field['manifest']['name'],
                'code' => $field['code'],
                'manifest' => $field['manifest']
            ],
        ];
    }

    public function getErrors(): bool|array
    {
        $errors = $this->errors;

        if (!$errors) {
            return false;
        }

        return [
            'status' => 'error',
            'count' => count($this->errors),
            'errors' => $this->errors
        ];
    }

    private function sanitize($fields, $params): bool|array
    {

        $arFields = [];

        foreach ($fields as $code => $field) {

            $value = trim($params[$code]);

            $field['code'] = $code;
            $field['value'] = $value;
            $field['length'] = mb_strlen($value, 'UTF-8');

            $arFields[$code] = $field;
        }

        if (!$arFields) {
            return false;
        }

        return $arFields;
    }


    public function setField($code, $manifest, $customValidation = false)
    {

        $this->fields[$code] = [
            'manifest' => $manifest
        ];

        return $this;

    }

}
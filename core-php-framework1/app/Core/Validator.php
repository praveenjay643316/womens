<?php

namespace App\Core;

/**
 * Minimal Laravel-style validator supporting pipe-delimited rules:
 * required, email, min:n, max:n, numeric, confirmed, unique:table,column[,ignoreId]
 *
 * On failure, errors and submitted input are flashed to the session and
 * the browser is redirected back to the referring page (script exits).
 */
class Validator
{
    protected static array $errors = [];

    public static function validate(array $data, array $rules): array
    {
        self::$errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $ruleString) as $rule) {
                self::applyRule($field, $value, $rule, $data);
            }
        }

        if (!empty(self::$errors)) {
            $_SESSION['errors']  = self::$errors;
            $_SESSION['old']     = $data;

            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            Response::redirect($referer);
        }

        return $data;
    }

    protected static function applyRule(string $field, mixed $value, string $rule, array $data): void
    {
        $params = [];

        if (str_contains($rule, ':')) {
            [$rule, $paramStr] = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }

        switch ($rule) {
            case 'required':
                if ($value === null || trim((string) $value) === '') {
                    self::addError($field, ucfirst($field) . ' is required.');
                }
                break;

            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    self::addError($field, ucfirst($field) . ' must be a valid email address.');
                }
                break;

            case 'min':
                if ($value !== null && $value !== '' && mb_strlen((string) $value) < (int) $params[0]) {
                    self::addError($field, ucfirst($field) . " must be at least {$params[0]} characters.");
                }
                break;

            case 'max':
                if ($value !== null && mb_strlen((string) $value) > (int) $params[0]) {
                    self::addError($field, ucfirst($field) . " must not exceed {$params[0]} characters.");
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    self::addError($field, ucfirst($field) . ' must be numeric.');
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (($data[$confirmField] ?? null) !== $value) {
                    self::addError($field, ucfirst($field) . ' confirmation does not match.');
                }
                break;

            case 'unique':
                self::validateUnique($field, $value, $params);
                break;
        }
    }

    /**
     * unique:table,column,ignoreId
     * Table/column names come from developer-written rule strings (not
     * user input), so they are safe to interpolate; the value itself is
     * always bound as a parameter.
     */
    protected static function validateUnique(string $field, mixed $value, array $params): void
    {
        if ($value === null || $value === '' || !isset($params[0])) {
            return;
        }

        $table    = $params[0];
        $column   = $params[1] ?? $field;
        $ignoreId = $params[2] ?? null;

        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :value";

        if ($ignoreId !== null && $ignoreId !== '') {
            $sql .= ' AND id != :ignore_id';
        }

        $db       = Database::getInstance()->getConnection();
        $stmt     = $db->prepare($sql);
        $bindings = ['value' => $value];

        if ($ignoreId !== null && $ignoreId !== '') {
            $bindings['ignore_id'] = $ignoreId;
        }

        $stmt->execute($bindings);

        if ((int) $stmt->fetchColumn() > 0) {
            self::addError($field, ucfirst($field) . ' has already been taken.');
        }
    }

    protected static function addError(string $field, string $message): void
    {
        self::$errors[$field][] = $message;
    }
}

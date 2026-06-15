<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Minimal rule-based validator.
 *
 * Supported rules: required, email, min:N, max:N, numeric, integer,
 * confirmed, in:a,b,c, url, unique:table,column[,ignoreId]
 */
final class Validator
{
    /** @var array<string,list<string>> */
    private array $errors = [];

    /**
     * @param array<string,mixed>      $data
     * @param array<string,string>     $rules  field => 'rule1|rule2:arg'
     */
    public function __construct(
        private readonly array $data,
        private readonly array $rules,
    ) {
    }

    /**
     * @param array<string,mixed>  $data
     * @param array<string,string> $rules
     */
    public static function make(array $data, array $rules): self
    {
        $validator = new self($data, $rules);
        $validator->run();
        return $validator;
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            foreach (explode('|', $ruleString) as $rule) {
                [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
                $this->applyRule($field, (string) $name, $arg, $value);
            }
        }
    }

    private function applyRule(string $field, string $rule, ?string $arg, mixed $value): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));
        $isEmpty = $value === null || $value === '';

        switch ($rule) {
            case 'required':
                if ($isEmpty) {
                    $this->add($field, "$label is required.");
                }
                break;
            case 'email':
                if (!$isEmpty && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->add($field, "$label must be a valid email address.");
                }
                break;
            case 'url':
                if (!$isEmpty && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->add($field, "$label must be a valid URL.");
                }
                break;
            case 'numeric':
                if (!$isEmpty && !is_numeric($value)) {
                    $this->add($field, "$label must be a number.");
                }
                break;
            case 'integer':
                if (!$isEmpty && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->add($field, "$label must be an integer.");
                }
                break;
            case 'min':
                if (!$isEmpty && mb_strlen((string) $value) < (int) $arg) {
                    $this->add($field, "$label must be at least $arg characters.");
                }
                break;
            case 'max':
                if (!$isEmpty && mb_strlen((string) $value) > (int) $arg) {
                    $this->add($field, "$label must not exceed $arg characters.");
                }
                break;
            case 'in':
                $allowed = explode(',', (string) $arg);
                if (!$isEmpty && !in_array((string) $value, $allowed, true)) {
                    $this->add($field, "$label is invalid.");
                }
                break;
            case 'confirmed':
                if ((string) $value !== (string) ($this->data[$field . '_confirmation'] ?? '')) {
                    $this->add($field, "$label confirmation does not match.");
                }
                break;
            case 'unique':
                if (!$isEmpty && $arg !== null) {
                    [$table, $column, $ignoreId] = array_pad(explode(',', $arg), 3, null);
                    $sql = "SELECT COUNT(*) FROM `$table` WHERE `$column` = ?";
                    $params = [$value];
                    if ($ignoreId !== null) {
                        $sql .= ' AND id <> ?';
                        $params[] = $ignoreId;
                    }
                    if ((int) Database::scalar($sql, $params) > 0) {
                        $this->add($field, "$label is already taken.");
                    }
                }
                break;
        }
    }

    private function add(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /**
     * @return array<string,list<string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * First error message overall, useful for flashing.
     */
    public function first(): ?string
    {
        foreach ($this->errors as $messages) {
            return $messages[0] ?? null;
        }
        return null;
    }
}

<?php

namespace App\Infrastructure;

use PDO;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private ?int $currentId;

    public function __construct(array $data, array $rules, ?int $currentId = null)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->currentId = $currentId;

        $this->validate();
    }

    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                switch ($rule) {
                    case 'required':
                        if (is_null($value) || $value === '') {
                            $this->errors[$field][] = 'is required';
                        }
                        break;

                    case 'string':
                        if (!is_null($value) && !is_string($value)) {
                            $this->errors[$field][] = 'must be a string';
                        }
                        break;

                    case 'int':
                        if (!is_null($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                            $this->errors[$field][] = 'must be an integer';
                        }
                        break;

                    case 'isbn':
                        if (!is_null($value) && !preg_match('/^[0-9Xx-]+$/', $value)) {
                            $this->errors[$field][] = 'must be a valid ISBN format';
                        }
                        break;
                }
            }
        }
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public static function make(array $data, array $rules, ?int $currentId = null): self
    {
        return new self($data, $rules, $currentId);
    }
}

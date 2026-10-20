<?php

declare(strict_types=1);

namespace KetPHP\Turnstile\Common\Support;

abstract class Relation
{

    public function __construct(private readonly array $data)
    {
    }

    protected function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->data) === true) {
            return $this->data[$key];
        }
        $current = $this->data;

        foreach (explode('.', $key) as $segment) {
            if (is_array($current) === true && array_key_exists($segment, $current) === true) {
                $current = $current[$segment];
            } else {
                return $default;
            }
        }
        return $current;
    }
}
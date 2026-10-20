<?php

declare(strict_types=1);

namespace KetPHP\Turnstile\Relation;

use KetPHP\Turnstile\Common\Support\Relation;

class TurnstileCodeRelation extends Relation
{

    public const KEY_NAME_CODE = 'code';
    public const KEY_NAME_ACTION = 'action';
    public const KEY_NAME_DESCRIPTION = 'description';

    /**
     * Get the error code string.
     *
     * @return string
     */
    public function getCode(): string
    {
        return (string) $this->get(static::KEY_NAME_CODE);
    }

    /**
     * Get the recommended action to resolve the error.
     *
     * @return string
     */
    public function getAction(): string
    {
        return (string) $this->get(static::KEY_NAME_ACTION);
    }

    /**
     * Get the detailed description of the error.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return (string) $this->get(static::KEY_NAME_DESCRIPTION);
    }
}
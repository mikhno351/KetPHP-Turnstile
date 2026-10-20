<?php

declare(strict_types=1);

namespace KetPHP\Turnstile\Common;

use KetPHP\Turnstile\Relation\TurnstileVerifyRelation;

interface TurnstileInterface
{

    public function verify(string $token, ?string $remoteIp = null): TurnstileVerifyRelation;
}
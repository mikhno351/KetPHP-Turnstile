<?php

declare(strict_types=1);

namespace KetPHP\Turnstile\Common\Support\Reference;

use KetPHP\Turnstile\Relation\TurnstileCodeRelation;

class TurnstileCode
{

    /** @var string Secret parameter not provided. */
    public const MISSING_INPUT_SECRET = 'missing-input-secret';

    /** @var string Response parameter was not provided. */
    public const MISSION_INPUT_RESPONSE = 'missing-input-response';

    /** @var string Secret key is invalid or expired. */
    public const INVALID_INPUT_SECRET = 'invalid-input-secret';

    /** @var string Token is invalid, malformed, or expired. */
    public const INVALID_INPUT_RESPONSE = 'invalid-input-response';

    /** @var string Request is malformed. */
    public const BAD_REQUEST = 'bad-request';

    /** @var string Token has already been validated. */
    public const TIMEOUT_OR_DUPLICATE = 'timeout-or-duplicate';

    /** @var string Internal error occurred. */
    public const INTERNAL_ERROR = 'internal-error';

    private readonly TurnstileCodeRelation $relation;

    /**
     * @param string $code The error code string returned by Cloudflare.
     */
    public function __construct(string $code)
    {
        [$action, $description] = match ($code) {
            self::MISSING_INPUT_SECRET => ['Ensure secret key is included', 'Secret parameter not provided'],
            self::MISSION_INPUT_RESPONSE => ['Ensure token is included', 'Response parameter was not provided'],
            self::INVALID_INPUT_SECRET => ['Check your secret key in the Cloudflare dashboard', 'Secret key is invalid or expired'],
            self::INVALID_INPUT_RESPONSE => ['User should retry the challenge', 'Token is invalid, malformed, or expired'],
            self::BAD_REQUEST => ['Check request format and parameters', 'Request is malformed'],
            self::TIMEOUT_OR_DUPLICATE => ['Each token can only be used once', 'Token has already been validated'],
            self::INTERNAL_ERROR => ['Retry the request', 'Internal error occurred'],
            default => ['Unknown', 'Unknown'],
        };

        $this->relation = new TurnstileCodeRelation([
            TurnstileCodeRelation::KEY_NAME_CODE => $code,
            TurnstileCodeRelation::KEY_NAME_ACTION => $action,
            TurnstileCodeRelation::KEY_NAME_DESCRIPTION => $description,
        ]);
    }

    /**
     * Create a new instance of TurnstileCode.
     *
     * @param string $code
     * @return static
     */
    public static function newInstance(string $code): static
    {
        return new static($code);
    }

    /**
     * Get the code relation object containing details about the error.
     *
     * @return TurnstileCodeRelation
     */
    public function getRelation(): TurnstileCodeRelation
    {
        return $this->relation;
    }
}
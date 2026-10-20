<?php

declare(strict_types=1);

namespace KetPHP\Turnstile\Relation;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use KetPHP\Turnstile\Common\Support\Reference\TurnstileCode;
use KetPHP\Turnstile\Common\Support\Relation;

class TurnstileVerifyRelation extends Relation
{
    public const KEY_NAME_RESPONSE = 'success';
    public const KEY_NAME_CHALLENGE_TIMESTAMP = 'challenge_ts';
    public const KEY_NAME_HOSTNAME = 'hostname';
    public const KEY_NAME_ERROR_CODES = 'error-codes';
    public const KEY_NAME_MESSAGES = 'messages';
    public const KEY_NAME_ACTION = 'action';
    public const KEY_NAME_CDATA = 'cdata';
    public const KEY_NAME_METADATA = 'metadata';
    public const KEY_NAME_METADATA_INTERACTIVE = 'metadata.interactive';
    public const KEY_NAME_METADATA_EPHEMERAL_ID = 'metadata.ephemeral_id';

    /**
     * Boolean indicating if validation was successful.
     *
     * @return bool
     */
    public function getSuccess(): bool
    {
        return (bool) $this->get(self::KEY_NAME_RESPONSE, false);
    }

    /**
     * Get the raw timestamp string when the challenge was solved.
     *
     * @return string|null
     */
    public function getChallengeTimestamp(): ?string
    {
        return $this->get(self::KEY_NAME_CHALLENGE_TIMESTAMP);
    }

    /**
     * Timestamp when the challenge was solved.
     *
     * @return DateTimeInterface
     * @throws Exception
     *
     * @see getChallengeTimestamp
     */
    public function getChallengeDateTime(): DateTimeInterface
    {
        return new DateTimeImmutable($this->getChallengeTimestamp());
    }

    /**
     * Hostname where the challenge was served.
     *
     * @return string
     */
    public function getHostname(): string
    {
        return (string) $this->get(self::KEY_NAME_HOSTNAME);
    }

    /**
     * Array of error codes (if validation failed).
     *
     * @return TurnstileCodeRelation[]
     */
    public function getErrorCodes(): array
    {
        return array_map(function ($code) {
            return TurnstileCode::newInstance($code)->getRelation();
        }, (array) $this->get(self::KEY_NAME_ERROR_CODES));
    }

    /**
     * Get any additional messages from the response.
     *
     * @return string[]
     */
    public function getMessages(): array
    {
        return (array) $this->get(self::KEY_NAME_MESSAGES);
    }

    /**
     * Custom action identifier from client-side.
     *
     * @return string
     */
    public function getAction(): string
    {
        return (string) $this->get(self::KEY_NAME_ACTION);
    }

    /**
     * Custom data payload from client-side.
     *
     * @return string
     */
    public function getCdata(): string
    {
        return (string) $this->get(self::KEY_NAME_CDATA);
    }

    /**
     * Get additional metadata provided by Cloudflare.
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return (array) $this->get(self::KEY_NAME_METADATA);
    }

    /**
     * Indicates whether the challenge was interactive.
     *
     * @return bool
     */
    public function getMetadataInteractive(): bool
    {
        return (bool) $this->get(self::KEY_NAME_METADATA_INTERACTIVE);
    }

    /**
     * Device fingerprint ID (Enterprise only).
     *
     * @return string
     */
    public function getMetadataEphemeralId(): string
    {
        return (string) $this->get(self::KEY_NAME_METADATA_EPHEMERAL_ID);
    }
}
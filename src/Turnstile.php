<?php

declare(strict_types=1);

namespace KetPHP\Turnstile;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use KetPHP\Turnstile\Common\Support\Reference\TurnstileCode;
use KetPHP\Turnstile\Common\TurnstileInterface;
use KetPHP\Turnstile\Exception\TurnstileException;
use KetPHP\Turnstile\Relation\TurnstileVerifyRelation;
use Psr\Log\LoggerInterface;

/**
 * Cloudflare Turnstile verification client.
 *
 * @link https://developers.cloudflare.com/turnstile/get-started
 */
class Turnstile implements TurnstileInterface
{

    /** @var string Cloudflare Turnstile API verification endpoint URL. */
    public const TURNSTILE_VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /** @var string Standard form field name for the Turnstile response token. */
    public const TURNSTILE_FIELD_NAME = 'cf-turnstile-response';

    /** @var int Maximum allowed length for the Turnstile token string. */
    public const TURNSTILE_FIELD_MAX_LENGTH = 2048;

    /** @var string Request payload field name for the secret key. */
    public const TURNSTILE_REQUEST_FIELD_NAME_SECRET = 'secret';

    /** @var string Request payload field name for the response token. */
    public const TURNSTILE_REQUEST_FIELD_NAME_RESPONSE = 'response';

    /** @var string Request payload field name for the user's remote IP address. */
    public const TURNSTILE_REQUEST_FIELD_NAME_REMOTE_IP = 'remoteip';

    private readonly Client $client;

    /**
     * @param string $secretKey The secret key provided by Cloudflare.
     * @param LoggerInterface|null $logger Optional PSR-3 logger instance.
     */
    public function __construct(private readonly string $secretKey, private readonly ?LoggerInterface $logger = null)
    {
        $this->client = new Client([
            'base_uri' => self::TURNSTILE_VERIFY_URL
        ]);
    }

    /**
     * Verify the client-provided Turnstile token.
     *
     * @param string $token The Turnstile response token.
     * @param string|null $remoteIp The user's remote IP address (optional).
     * @return TurnstileVerifyRelation
     * @throws TurnstileException If the token is empty, too long, or JSON decoding fails.
     */
    public function verify(string $token, ?string $remoteIp = null): TurnstileVerifyRelation
    {
        if (empty($token) === true) {
            throw new TurnstileException('Turnstile token cannot be empty.');
        }
        if (strlen($token) > self::TURNSTILE_FIELD_MAX_LENGTH) {
            throw new TurnstileException('Turnstile token too long.');
        }

        try {
            $response = $this->send(array_filter([
                self::TURNSTILE_REQUEST_FIELD_NAME_SECRET => $this->secretKey,
                self::TURNSTILE_REQUEST_FIELD_NAME_RESPONSE => $token,
                self::TURNSTILE_REQUEST_FIELD_NAME_REMOTE_IP => $remoteIp
            ]));
        } catch (GuzzleException $exception) {
            $response = [
                TurnstileVerifyRelation::KEY_NAME_RESPONSE => false,
                TurnstileVerifyRelation::KEY_NAME_ERROR_CODES => [
                    TurnstileCode::INTERNAL_ERROR
                ]
            ];
            $this->logger?->error($exception);
        }

        return new TurnstileVerifyRelation($response);
    }

    /**
     * @param array $data Request payload data.
     * @return array Decoded JSON response.
     *
     * @throws GuzzleException If a network or HTTP error occurs.
     * @throws TurnstileException If the response is not valid JSON.
     */
    private function send(array $data): array
    {
        $response = $this->client->post('', [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            RequestOptions::JSON => $data,
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TurnstileException(json_last_error_msg());
        }

        return $result;
    }
}
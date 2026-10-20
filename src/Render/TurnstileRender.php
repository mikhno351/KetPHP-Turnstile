<?php

declare(strict_types=1);

namespace KetPHP\Turnstile\Render;

use KetPHP\Turnstile\Exception\TurnstileException;

/**
 * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations
 */
final class TurnstileRender
{

    public const TURNSTILE_SCRIPT_URL = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

    /** @var string The challenge runs automatically after calling the `render()` function and provides immediate protection as soon as the widget loads. The challenge runs in the background while the page loads, ensuring the token is ready when the visitor submits data. */
    public const EXECUTION_RENDER = 'render';

    /** @var string The challenge runs after calling the `turnstile.execute()` function separately and gives you precise control over when verification occurs. This option is useful for multi-step forms, conditional verification, or when you want to defer the challenge until the visitor actually attempts to submit data. This can improve page load performance and visitor experience by only running verification when needed. */
    public const EXECUTION_EXECUTE = 'execute';

    /** @var string The default size works well for most desktop and mobile layouts. Use this if you have adequate horizontal space on your website or form. */
    public const SIZE_NORMAL = 'normal';

    /** @var string Automatically adapts to the container width while maintaining minimum usability. Use this for responsive designs that need to work across all screen sizes. */
    public const SIZE_FLEXIBLE = 'flexible';

    /** @var string Ideal for mobile interfaces, sidebars, or any space where horizontal space is limited. The compact widget is taller than normal to accommodate the smaller width. */
    public const SIZE_COMPACT = 'compact';

    /** @var string Automatically matches the visitor's system theme preference. Auto is recommended for most implementations as it respects the visitor's preferences and provides the best accessibility experience. */
    public const THEME_AUTO = 'auto';

    /** @var string Light theme with bright colors and clear contrast. Light theme works best on bright backgrounds and provides high contrast for readability. */
    public const THEME_LIGHT = 'light';

    /** @var string Dark theme optimized for dark interfaces. Dark theme is ideal for dark interfaces, gaming sites, or applications with dark color schemes. */
    public const THEME_DARK = 'dark';

    /** @var string The widget is always visible from page load. This is the best option for most implementations where you want your visitors to see the widget immediately as it provides clear visual feedback that security verification is in place. */
    public const APPEARANCE_ALWAYS = 'always';

    /** @var string The widget only becomes visible after the challenge begins. This is useful for when you need to control the timing of widget appearance, such as showing it only when a visitor starts filling out a form or selecting a submit button. */
    public const APPEARANCE_EXECUTE = 'execute';

    /** @var string The widget becomes visible only when visitor interaction is required and provides the cleanest visitor experience. Most visitors will never see the widget, but suspected bots will encounter the interactive challenge. */
    public const APPEARANCE_INTERACTION_ONLY = 'interaction-only';

    /** @var string Automatically retries failed challenges. Auto retry provides better visitor experience by automatically recovering from temporary network issues or processing errors. */
    public const RETRY_AUTO = 'auto';

    /** @var string Disables automatic retry. This requires manual intervention and gives you full control over error handling in applications that need custom retry logic. */
    public const RETRY_NEVER = 'never';

    /** @var string Refresh provides seamless visitor experience but uses more resources. */
    public const REFRESH_AUTO = 'auto';

    /** @var string Refresh gives visitors control but requires them to take action. */
    public const REFRESH_MANUAL = 'manual';

    /** @var string Refresh requires your application to handle all refresh logic. */
    public const REFRESH_NEVER = 'never';

    public const TURNSTILE_ACTION_MAX_LENGTH = 32;
    public const TURNSTILE_CDATA_MAX_LENGTH = 255;

    /** @var string[] */
    private array $attributes = [];

    /** @var string[] */
    private array $classes = [];

    /**
     * @param string $siteKey The site key provided by Cloudflare.
     */
    public function __construct(string $siteKey)
    {
        $this->withClass('cf-turnstile')->withAttribute('data-sitekey', $siteKey);
    }

    /**
     * Create a new instance of TurnstileRender.
     *
     * @param string $siteKey The site key provided by Cloudflare.
     * @return self
     */
    public static function newInstance(string $siteKey): self
    {
        return new self($siteKey);
    }

    /**
     * Add or replace a custom HTML/widget attribute.
     *
     * @param string $attributeName The attribute name (e.g. 'data-theme', 'data-action').
     * @param string $value The attribute value.
     * @return self
     */
    public function withAttribute(string $attributeName, string $value): self
    {
        $this->attributes[htmlspecialchars($attributeName, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return $this;
    }

    /**
     * Control when the challenge runs and a token is generated.
     *
     * @param string $value Use TurnstileRender::EXECUTION_* constants.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#execution-modes
     */
    public function withDataExecution(string $value): self
    {
        return $this->withAttribute('data-execution', $value);
    }

    /**
     * The Turnstile widget can have two different fixed sizes or a flexible width size when using the Managed or Non-Interactive modes.
     *
     * @param string $size Use TurnstileRender::SIZE_* constants.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#widget-sizes
     */
    public function withDataSize(string $size): self
    {
        return $this->withAttribute('data-size', $size);
    }

    /**
     * Customize the widget's visual appearance to match your website's design.
     *
     * @param string $theme Use TurnstileRender::THEME_* constants.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#theme-options
     */
    public function withDataTheme(string $theme): self
    {
        return $this->withAttribute('data-theme', $theme);
    }

    /**
     * Control when the widget becomes visible to visitors using the appearance mode.
     *
     * @param string $appearance Use TurnstileRender::APPEARANCE_* constants.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#appearance-modes
     */
    public function withDataAppearance(string $appearance): self
    {
        return $this->withAttribute('data-appearance', $appearance);
    }

    /**
     * Set the language for the widget interface.
     *
     * @param string $languageCode Specific language codes: ISO 639-1 two-letter codes, such as es, fr, de.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#language-configuration
     */
    public function withDataLanguage(string $languageCode): self
    {
        return $this->withAttribute('data-language', $languageCode);
    }

    /**
     * Triggered when the challenge is successfully completed.
     *
     * @param string $callback Frontend function name (eg. onSuccess)
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#callback-configuration
     */
    public function withDataCallback(string $callback): self
    {
        return $this->withAttribute('data-callback', $callback);
    }

    /**
     * Triggered when an error occurs during the challenge.
     *
     * @param string $callback Frontend function name (eg. onError)
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#callback-configuration
     */
    public function withDataCallbackError(string $callback): self
    {
        return $this->withAttribute('data-error-callback', $callback);
    }

    /**
     * Triggered when a token expires (before timeout).
     *
     * @param string $callback Frontend function name (eg. onExpired)
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#callback-configuration
     */
    public function withDataCallbackExpired(string $callback): self
    {
        return $this->withAttribute('data-expired-callback', $callback);
    }

    /**
     * Triggered when an interactive challenge times out.
     *
     * @param string $callback Frontend function name (eg. onTimeout)
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#callback-configuration
     */
    public function withDataCallbackTimeout(string $callback): self
    {
        return $this->withAttribute('data-timneout-callback', $callback);
    }

    /**
     * Control how Turnstile handles failed challenges.
     *
     * @param int|string $value Use TurnstileRender::RETRY_* constants or enter time in milliseconds.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#retry-behavior
     */
    public function withDataRetry(int|string $value): self
    {
        list($attributeName, $attributeValue) = match (is_numeric($value) === true) {
            true => ['data-retry-interval', (string) $value],
            false => ['data-retry', $value],
        };
        return $this->withAttribute($attributeName, $attributeValue);
    }

    /**
     * Control how Turnstile handles token expiration.
     *
     * @param string $value Use TurnstileRender::REFRESH_* constants.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#refresh-behavior
     */
    public function withDataRefreshExpired(string $value): self
    {
        return $this->withAttribute('data-refresh-expired', $value);
    }

    /**
     * Control how Turnstile handles token interactive timeouts.
     *
     * @param string $value Use TurnstileRender::REFRESH_* constants.
     * @return $this
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#refresh-behavior
     */
    public function withDataRefreshTimeout(string $value): self
    {
        return $this->withAttribute('data-refresh-timeout', $value);
    }

    /**
     * Add custom identifiers to your challenges.
     *
     * @param string $value A custom identifier for analytics and differentiation (maximum 32 characters).
     * @return $this
     *
     * @throws TurnstileException The attribute "data-action" contained value too long.
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#custom-data
     */
    public function withDataAction(string $value): self
    {
        if (mb_strlen($value, 'UTF-8') > self::TURNSTILE_ACTION_MAX_LENGTH) {
            throw new TurnstileException('The attribute "data-action" contained value too long.');
        }
        return $this->withAttribute('data-action', $value);
    }

    /**
     * Add custom data to your challenges.
     *
     * @param string $value Custom payload data returned during validation (maximum 255 characters).
     * @return $this
     *
     * @throws TurnstileException The attribute "data-cdata" contained value too long.
     * @throws TurnstileException The attribute "data-cdata" contains invalid characters.
     *
     * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/widget-configurations/#custom-data
     */
    public function withDataCdata(string $value): self
    {
        if (strlen($value) > self::TURNSTILE_CDATA_MAX_LENGTH) {
            throw new TurnstileException('The attribute "data-cdata" contained value too long.');
        }
        if (preg_match('/^[a-zA-Z0-9_\-]+$/', $value) !== 1) {
            throw new TurnstileException('The attribute "data-cdata" contains invalid characters. Only ASCII letters, numbers, underscores, and hyphens are allowed.');
        }
        return $this->withAttribute('data-cdata', $value);
    }

    public function withClass(string ...$classNames): self
    {
        $this->classes = array_unique(array_merge($this->classes, $classNames));
        return $this;
    }

    /**
     * Render the Turnstile widget HTML element without script.
     *
     * @return string
     */
    public function render(): string
    {
        $result = [];

        foreach (array_merge($this->attributes, [
            'class' => implode(' ', $this->classes),
        ]) as $key => $value) {
            $result[] = sprintf('%s="%s"', $key, $value);
        }

        return sprintf('<div %s></div>', implode(' ', $result));
    }
}
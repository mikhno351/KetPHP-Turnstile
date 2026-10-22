# KetPHP Turnstile

![Packagist Version](https://img.shields.io/packagist/v/ket-php/turnstile)
![Packagist Downloads](https://img.shields.io/packagist/dt/ket-php/turnstile?logo=packagist&logoColor=white)
![Static Badge](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)

A lightweight and flexible Cloudflare Turnstile library for PHP applications supporting widget rendering, customization, and response verification.

## Features
- **Frontend Render Builder** - Easily configure and render Turnstile widgets with fluent builder methods
- **Token Verification** - Verify client Turnstile tokens against Cloudflare's `siteverify` API
- **Structured Response Object** - Work with typed relations and error objects instead of raw arrays
- **PSR-3 Logging Support** - Integrated exception logging for network or API failures
- **Full Configuration Support** - Custom execution modes, themes, sizes, appearances, callbacks, retry/refresh behaviors, actions, and custom data (`cdata`)

## Installation
```composer
composer require ket-php/turnstile
```

## Usage

### Render the Turnstile Widget (Frontend)

Use `TurnstileRender` to configure the frontend widget container and script options.

```php
use KetPHP\Turnstile\Render\TurnstileRender;

// Create instance
$render = TurnstileRender::newInstance('YOUR_SITE_KEY')
    ->withClass('your', 'custom', 'classes')
    ->withAttribute('your-custom-attribute', 'true')
    ->withDataTheme(TurnstileRender::THEME_DARK)
    ->withDataSize(TurnstileRender::SIZE_NORMAL)
    ->withDataAppearance(TurnstileRender::APPEARANCE_ALWAYS)
    ->withDataExecution(TurnstileRender::EXECUTION_RENDER)
    ->withDataAction('login_form')
    ->withDataCdata('user_session_id')
    ->withDataCallback('onTurnstileSuccess')
    ->withDataCallbackError('onTurnstileError');

// Output the HTML container div
echo $render->render();
// Output: <div class="cf-turnstile" data-sitekey="YOUR_SITE_KEY" data-theme="dark" ...></div>
```

#### Render Script Tag
```php
// Standard async & defer script tag
echo '<script src="' . TurnstileRender::TURNSTILE_SCRIPT_URL . '" async defer></script>';
```

---

### Verify Response Token (Backend)

Use `Turnstile` to verify client-submitted tokens (`cf-turnstile-response`).

```php
use KetPHP\Turnstile\Turnstile;
use KetPHP\Turnstile\Exception\TurnstileException;

$turnstile = new Turnstile(
    secretKey: 'YOUR_SECRET_KEY',
    logger: $psr3Logger // Optional PSR-3 Logger instance
);

$token = $_POST[Turnstile::TURNSTILE_FIELD_NAME] ?? '';
$remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

try {
    $relation = $turnstile->verify($token, $remoteIp);

    if ($relation->getSuccess()) {
        echo "Challenge solved on: " . $relation->getHostname();
        echo "Timestamp: " . $relation->getChallengeDateTime()->format('Y-m-d H:i:s');
    } else {
        foreach ($relation->getErrorCodes() as $error) {
            echo "Error Code: " . $error->getCode() . "\n";
            echo "Action: " . $error->getAction() . "\n";
            echo "Description: " . $error->getDescription() . "\n";
        }
    }
} catch (TurnstileException $e) {
    echo "Validation failed: " . $e->getMessage();
}
```

---

### Working with `TurnstileVerifyRelation`

The `verify()` method returns a `TurnstileVerifyRelation` object providing typed accessors for all Cloudflare payload fields:

```php
$relation = $turnstile->verify($token, $remoteIp);

$success     = $relation->getSuccess();                // bool
$hostname    = $relation->getHostname();               // string
$rawTs       = $relation->getChallengeTimestamp();     // string|null
$dateTime    = $relation->getChallengeDateTime();      // DateTimeInterface
$action      = $relation->getAction();                 // string
$cdata       = $relation->getCdata();                  // string
$errorCodes  = $relation->getErrorCodes();             // TurnstileCodeRelation[]
$metadata    = $relation->getMetadata();               // array
$interactive = $relation->getMetadataInteractive();    // bool
$ephemeralId = $relation->getMetadataEphemeralId();    // string (Enterprise)
```

---

### Handling Error Codes

`TurnstileCode` maps standard Cloudflare error codes to actionable descriptions via `TurnstileCodeRelation`:

```php
use KetPHP\Turnstile\Common\Support\Reference\TurnstileCode;

$codeRelation = TurnstileCode::newInstance(TurnstileCode::INVALID_INPUT_RESPONSE)->getRelation();

echo $codeRelation->getCode();        // "invalid-input-response"
echo $codeRelation->getAction();      // "User should retry the challenge"
echo $codeRelation->getDescription(); // "Token is invalid, malformed, or expired"
```

# OAuth2 client [ARCHIVED - NOT MAINTAINED]

An implementation of OAuth2 client implementation using PHP 7

This is an example for demonstration. Not for production use.

- PHP 7.0 and higher.
- Integrates with Facebook, Google and Yahoo.

## Structure

All sitewide configuration is stored in the __config.php__ file
(Including Client id, secret and redirect uri).

The __Config class__ uses a singleton pattern and loads the configuration.

The __ProviderFactory class__ uses a simple factory pattern and returns a provider instance.

All providers extend the __AbstractProvider class__.

The __curlRequestTrait__ provides the __makeRequest__ method to the AbstractProvider.

## Notes:
For security reasons config.php should be placed outside the public folder.

For HTTPS requests a certificate file must be added and the path specified in the curlRequestTrait.php file.

Facebook requires an App id and must be specified in the __Auth/Provider/Facebook.php__.

## Example usage

```php
<?php
use Auth\ProviderFactory;

require __DIR__ .'/vendor/autoload.php';

$config   = Config::getInstance()->get('OAuth');
$provider = ProviderFactory::get($config);


if (isset($_GET['error']) ) {

    // Got an error, probably user denied access
    exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));

} elseif (! isset($_GET['code'])) {

    // No authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    header('Location: ' . $authUrl);
    exit;

} elseif (empty($_GET['state'])
    || isset($_SESSION['oauth2state'])
    && $_GET['state'] !== $_SESSION['oauth2state']
) {

    // State is invalid, possible CSRF attack in progress
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token
    $token = $provider->getAccessToken($_GET['code']);

    // Look up a users profile data
    $provider->getResourceOwner($token);

    echo $provider->getEmail();
    echo $provider->getName();

}

```

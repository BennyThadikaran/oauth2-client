<?php
namespace Auth;

class ProviderFactory {

  public static function get(array $config) {
      switch ($_SERVER['REQUEST_URI']) {
        case '/facebook-login':
          $key = $config['facebook'];
          $class = 'Auth\Provider\Facebook';
          break;

        case '/google-login':
          $key = $config['google'];
          $class = 'Auth\Provider\Google';
          break;

        case '/y-login':
          $key = $config['yahoo'];
          $class = 'Auth\Provider\Yahoo';
          break;
      }
      return new $class($key['id'], $key['secret'], $key['redirect_uri']);
  }
}

<?php
namespace Auth\Provider;
/**
* Represents a service provider (authorization server).
*/
abstract class AbstractProvider
{
  use \CurlRequestTrait;

  protected $state;
  protected $clientId;
  protected $redirectUri;
  protected $clientSecret;

  public function __construct($clientId, $secret, $redirectUri)
  {
      $this->clientId     = $clientId;
      $this->secret       = $secret;
      $this->redirectUri  = $redirectUri;
      $this->state        = bin2hex(random_bytes(16));

      $_SESSION['oauth2state'] = $this->state;
  }

  abstract public function getAuthorizationUrl();

  abstract public function getAccessToken($code);

  abstract public function getResourceOwner($token);

  abstract public function getEmail();

  abstract public function getName();

}

<?php
namespace Auth\Provider;
/**
* Facebook Oauth provider implements Abstract Provider
*/
class Facebook extends AbstractProvider
{
  private $baseUri = 'https://graph.facebook.com/v3.0/';
  private $appId = ''; // YOUR APP ID HERE
  private $owner;

  public function getAuthorizationUrl():string
  {
      return $this->baseUri . 'dialog/oauth?' . $this->getAuthorizationParams();
  }

  public function getAccessToken($code)
  {
      $url = $this->baseUri . 'oauth/access_token?' . $this->getAccessTokenParams($code);

      return $this->makeRequest($url);
  }

  public function getResourceOwner($token)
  {
      $query  = http_build_query([
          'input_token'   => $token->access_token,
          'access_token'  => "{$this->appId}|{$this->secret}"
      ], null, '&', PHP_QUERY_RFC3986);

      $url    = 'https://graph.facebook.com/debug_token/?' . $query;
      $user   = $this->makeRequest($url);

      $url = sprintf("%s%s?access_token=%s&fields=name,email",
          $this->baseUri,
          $user->data->user_id,
          $token->access_token
      );

      $data = $this->makeRequest($url);

      $this->owner = [
          'name'  => $data->name,
          'email' => $data->email ?? null,
      ];
  }

  public function getEmail():string
  {
      return $this->owner['email'];
  }

  public function getName():string
  {
      return $this->owner['name'];
  }

  private function getAuthorizationParams():string
  {
      $params = [
          'client_id'     => $this->clientId,
          'redirect_uri'  => $this->redirectUri,
          'scope'         => 'public_profile email',
          'state'         => $this->state,
          'response_type' => 'code'
      ];

      return http_build_query($params, null, '&', \PHP_QUERY_RFC3986);
  }

  private function getAccessTokenParams($code):string
  {
      $params = [
          'code'          => $code,
          'client_id'     => $this->clientId,
          'client_secret' => $this->secret,
          'redirect_uri'  => $this->redirectUri
      ];

      return http_build_query($params, null, '&', \PHP_QUERY_RFC3986);
  }
}

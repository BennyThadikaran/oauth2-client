<?php
namespace Auth\Provider;
/**
* Google Oauth provider implements Abstract Provider
*/
class Yahoo extends AbstractProvider
{
  private $baseUri = 'https://api.login.yahoo.com/oauth2/';
  private $owner;

  public function getAuthorizationUrl()
  {
      $params = $this->getAuthorizationParams();

      return $this->baseUri . 'request_auth?' . $params;
  }

  public function getAccessToken($code)
  {
      $url = $this->baseUri . 'get_token';
      $params = [
          'Authorization: Basic ' . base64_encode("$this->clientId:$this->secret"),
          'Content-Type: application/x-www-form-urlencoded'
      ];

      return $this->makeRequest($url, [
          'method'  => 'POST',
          'params'  => $this->getAccessTokenParams($code),
          'headers' => $params
      ]);
  }

  public function getResourceOwner($token)
  {
      $accessToken = $token->access_token ?? null;
      $guid = $token->xoauth_yahoo_guid;

      $url = "https://social.yahooapis.com/v1/user/{$guid}/profile?format=json";

      $data = $this->makeRequest($url, [
        'headers' => [ "Authorization: Bearer {$accessToken}" ]
      ]);

      $this->owner = [
          'name' => "{$data->profile->givenName} {$data->profile->familyName}",
          'email' => $data->profile->emails ?? null
      ];
  }

  public function getName()
  {
      return $this->owner['name'];
  }

  public function getEmail()
  {
      foreach ($this->owner['email'] as $key) {
          if ($key->primary) {
              return $key->handle;
          }
      }
      return null;
  }

  private function getAuthorizationParams():string
  {
      $params = [
          'client_id'     => $this->clientId,
          'redirect_uri'  => $this->redirectUri,
          'state'         => $this->state,
          'response_type' => 'code',
          'language'      => 'en-us'
      ];

      return http_build_query($params, null, '&', \PHP_QUERY_RFC3986);
  }

  private function getAccessTokenParams($code):string
  {
      $params = [
          'code'          => $code,
          'client_id'     => $this->clientId,
          'client_secret' => $this->secret,
          'redirect_uri'  => $this->redirectUri,
          'grant_type'    => 'authorization_code'
      ];

      return http_build_query($params, null, '&', \PHP_QUERY_RFC3986);
  }

  private function getScope():string
  {
      $profile = 'https://www.googleapis.com/auth/userinfo.profile';
      $email = 'https://www.googleapis.com/auth/userinfo.email';
      return  "$profile $email";
  }
}

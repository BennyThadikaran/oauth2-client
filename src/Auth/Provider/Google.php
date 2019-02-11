<?php
namespace Auth\Provider;
/**
* Google Oauth provider implements Abstract Provider
*/
class Google extends AbstractProvider
{
  private $owner;

  public function getAuthorizationUrl()
  {
      $params = $this->getAuthorizationParams();

      return 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
  }

  public function getAccessToken($code)
  {
      $params = $this->getAccessTokenParams($code);
      $url    = 'https://www.googleapis.com/oauth2/v4/token';

      return $this->makeRequest($url, [
          'method' => 'POST',
          'params' => $params
      ]);
  }

  public function getResourceOwner($token)
  {
      $url = 'https://www.googleapis.com/userinfo/v2/me';
      $data = $this->makeRequest($url, [
          'headers' => [ "Authorization: Bearer {$token->access_token}" ]
      ]);

      $this->owner = [
          'name'  => $data->name,
          'email' => $data->email ?? null
      ];
  }

  public function getName()
  {
      return $this->owner['name'];
  }

  public function getEmail()
  {
      return $this->owner['email'];
  }

  private function getAuthorizationParams():string
  {
      $params = [
          'client_id'     => $this->clientId,
          'redirect_uri'  => $this->redirectUri,
          'scope'         => $this->getScope(),
          'state'         => $this->state,
          'response_type' => 'code',
          'access_type'   => 'offline',
          'prompt'        => 'consent'
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
      return "$profile $email";
  }
}

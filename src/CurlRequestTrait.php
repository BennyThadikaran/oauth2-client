<?php
trait CurlRequestTrait {
  protected $certificateFile = __DIR__ . '/../cacert.pem';

  public function makeRequest(string $url, string $method = 'GET', array $options=[])
  {
      if (! in_array($method, ['GET', 'POST'])) {
          throw new Exception('Invalid method in makeRequest function');
      }

      $defaultOpts = [
          'method'    => 'GET',
          'params'    => [],
          'headers'   => [],
          'debug'     => false,
          'debugFile' => 'curlDebug.txt'
      ];

      extract(array_merge($defaultOpts, $options));

      $ch = curl_init($url);

      curl_setopt_array($ch, [
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYPEER => true,
          CURLOPT_CAINFO         => $this->certificateFile,
          CURLOPT_SSL_VERIFYHOST => 2,
          CURLOPT_HEADER         => true,
          CURLOPT_CUSTOMREQUEST  => $method
      ]);

      if ($debug) {
          ob_start();
          $out = fopen('php://output', 'w');

          curl_setopt($ch, CURLOPT_VERBOSE, true);
          curl_setopt($ch, CURLOPT_STDERR, $out);
      }

      if (! empty($header)) {
          curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      }

      if ($method === "POST") {
          curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      }

      $output = curl_exec($ch);
      $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
      $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      $body = substr($output, $headerSize);
      curl_close($ch);

      if ($debug) {
          fclose($out);
          $debug = ob_get_clean() . "\n\n" . $output;
          file_put_contents($debugFile, $debug, FILE_APPEND);
      }

      return $contentType === 'application/json'
          ? json_decode($body)
          : $body;
  }
}

<?php

class Config {
  private $data;
  private static $instance;

  /*
  * For security reasons the config.php file
  * should be placed outside the public folder.
  * Specify the file location in the constructor.
  */
  private function __construct()
  {
      $this->data = require __DIR__ . '/../config.php';
  }

  public static function getInstance():self
  {
      if (self::$instance == null) {
          self::$instance = new Config();
      }
      return self::$instance;
  }

  public function get(string $key):array
  {
      if (!isset($this->data[$key])) {
          echo $key . " does not exist";
      }
      return $this->data[$key];
  }
}

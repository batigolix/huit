<?php

namespace Drupal\tic;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Class IcdApiConnectService.
 */
class IcdApiConnectService implements IcdApiConnectInterface {

  const TOKEN_ENPOINT = "https://icdaccessmanagement.who.int/connect/token";

  const CLIENT_ID = "a470e5e9-8528-44f8-97a9-775f075e05e3_4d8978e4-1200-4a59-88fa-b2df4118dd62";
  const CLIENT_SECRET = "W0IuXlnbT/QoS4Z4anigvzIn3jSPjoJSxYdenD9DcVA=";


  const SCOPE = "icdapi_access";
  const GRANT_TYPE = "client_credentials";


  private $token;
  private $uri;
  private $api_response;


  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Drupal\Core\Language\LanguageManagerInterface definition.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * ICDAPIClient constructor - need session_start()
   * @param string $uri
   */
  /**
   * Constructs a new IcdApiConnectService object.
   */
  public function __construct($uri, ConfigManagerInterface $config_manager, LanguageManagerInterface $language_manager) {
    $this->configManager = $config_manager;
    $this->languageManager = $language_manager;

    $this->uri = $uri;

    if(isset($_SESSION['token'])) {
      $this->token = $_SESSION['token'];
    }
    else {
      $this->newToken();
    }
  }


  /**
   * Make the get request
   * @return json
   */
  public function get() {

    if($this->makeRequest() == 401) { // unauthorized token
      $this->newToken();
      $this->makeRequest();
    }
    return json_decode($this->api_response);
  }



  /**
   * Make the curl request
   * @return int $http_code
   */
  private function makeRequest() {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->uri);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Authorization: Bearer '.$this->token,
      'Accept: application/json',
      'Accept-Language: en',
      'API-Version: v2'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // set curl without result echo
    $this->api_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_code;
  }


  /**
   * Request an OAUTH 2.0 token from the server
   */
  private function newToken() {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::TOKEN_ENPOINT);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
      'client_id' => self::CLIENT_ID,
      'client_secret' => self::CLIENT_SECRET,
      'scope' => self::SCOPE,
      'grant_type' => self::GRANT_TYPE
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // set curl without result echo
    $result = curl_exec($ch);
    curl_close($ch);

    $json_array = (json_decode($result, true));
    $this->token = $json_array['access_token'];
    $_SESSION['token'] = $this->token;
  }



}

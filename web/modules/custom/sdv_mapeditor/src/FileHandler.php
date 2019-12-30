<?php

namespace Drupal\sdv_mapeditor;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Archiver\ArchiverManager;
use Drupal\Core\Archiver\Zip;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

/**
 * Class DownloadUrl.
 */
class FileHandler implements FileHandlerInterface {

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The archiver manager service.
   *
   * @var \Drupal\Core\Archiver\ArchiverManager
   */
  protected $pluginManagerArchiver;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * A configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Constructs a new FileHandler object.
   */
  public function __construct(FileSystemInterface $file_system, LoggerInterface $logger, ArchiverManager $plugin_manager_archiver, MessengerInterface $messenger, ConfigFactory $configFactory) {
    $this->fileSystem = $file_system;
    $this->logger = $logger;
    $this->pluginManagerArchiver = $plugin_manager_archiver;
    $this->messenger = $messenger;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function createFolder($path) {

    // Deletes the folder before creating it.
    $this->fileSystem->deleteRecursive($path);

    // Creates the folder and logs the result.
    if ($this->fileSystem->prepareDirectory($path, $this->fileSystem::CREATE_DIRECTORY)) {
      $this->logger->notice("The folder ${path} has been created.");
      $this->messenger->addStatus("The folder ${path} has been created.");
      return $path;
    }
    else {
      $this->logger->error("The folder ${path} could not be created.");
      $this->messenger->addError("The folder ${path} could not be created.");
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function download($url, $path) {

    // Fetches the filename from the URL.
    $basename = basename($url);

    // Sets the destination path.
    $destination = "${path}/${basename}";

    // Downloads the file to a local folder.
    $file = system_retrieve_file($url, "${destination}", FALSE, $this->fileSystem::EXISTS_REPLACE);

    // Logs results and returns the result.
    if ($file) {
      $this->logger->notice("The file ${url} has been saved to ${destination}.");
      $this->messenger->addStatus("The file ${url} has been saved to ${destination}.");
      return $file;
    }
    else {
      $this->logger->error("Could not save ${url} to ${destination}.");
      $this->messenger->addError("Could not save ${url} to ${destination}.");
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function extract($file, $path) {

    // Extracts the file to the local folder.
    $fileRealPath = $this->fileSystem->realpath($file);
    $zip = new Zip($fileRealPath);
    $zip->extract($path);
  }

  /**
   * {@inheritdoc}
   */
  public function list($dir, $mask) {
    return file_scan_directory($dir, $mask);
  }

  /**
   * {@inheritdoc}
   */
  public function checkIfExists($path) {

    // Checks if file is external.
    if (UrlHelper::isExternal($path) && $this->checkUrl($path)) {
      return TRUE;
    }
    $root = \Drupal::root();
    if (file_exists($root . $path)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function checkUrl($url) {
    $client = \Drupal::httpClient();
    try {
      $response = $client->get($url, ['headers' => ['Accept' => 'text/plain']]);
      $data = (string) $response->getBody();
      if (empty($data)) {
        $this->logger->error('URL does not contain data');
        $this->messenger->addError('URL does not contain data');
        return FALSE;
      }
    }
    catch (RequestException $e) {
      $this->logger->error("External URL cannot be reached. Error message: {$e->getMessage()}.");
      $this->messenger->addError("External URL cannot be reached. Error message: {$e->getMessage()}.");
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getWms() {
    $this->config = $this->configFactory->get('sdv_mapeditor.settings');
    $wms_url = $this->config->get('wms_url');
    $xml = simplexml_load_file($wms_url);
    $wms = '';
    if ($xml->Capability->Layer) {
      foreach ($xml->Capability->Layer->children() as $layer) {
        $name = $layer->Name;
        $title = $layer->Title;
        if ($name != '') {
          $wms .= ($wms == '' ? '' : '|') . $name . '=' . $title;
        }
      }
    }
    return $wms;
  }

}

<?php

namespace Drupal\sdv_mapeditor;

use Drupal\Core\Archiver\ArchiverManager;
use Drupal\Core\Archiver\Zip;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DownloadUrl.
 */
class FileHandler implements FileHandlerInterface {

  /**
   * Drupal\Core\File\FileSystemInterface definition.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \Drupal\Core\Archiver\ArchiverManager
   */
  protected $pluginManagerArchiver;

  /**
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new FileHandler object.
   */
  public function __construct(FileSystemInterface $file_system, LoggerInterface $logger, ArchiverManager $plugin_manager_archiver, MessengerInterface $messenger) {
    $this->fileSystem = $file_system;
    $this->logger = $logger;
    $this->pluginManagerArchiver = $plugin_manager_archiver;
    $this->messenger = $messenger;
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
    $root = \Drupal::root();
    if (file_exists($root . $path)) {
      return TRUE;
    }
    return FALSE;
  }
}

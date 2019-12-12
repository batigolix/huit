<?php

namespace Drupal\sdv_mapeditor;

use Drupal\Core\Archiver\ArchiverManager;
use Drupal\Core\Archiver\Zip;
use Drupal\Core\File\FileSystemInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DownloadUrl.
 */
class DownloadUrl implements DownloadUrlInterface {

  /**
   * Drupal\Core\File\FileSystemInterface definition.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  protected $logger;

  protected $pluginManagerArchiver;

  /**
   * Constructs a new DownloadUrl object.
   */
  public function __construct(FileSystemInterface $file_system, LoggerInterface $logger, ArchiverManager $plugin_manager_archiver) {
    $this->fileSystem = $file_system;
    $this->logger = $logger;
    $this->pluginManagerArchiver = $plugin_manager_archiver;
  }

  public function createFolder($path) {

    if ($this->fileSystem->prepareDirectory($path, $this->fileSystem::CREATE_DIRECTORY)) {
      $this->logger->notice("${path} created");
      return $path;
    }
    else {
      $this->logger->error("${path} could not be created");
      return false;
    }

  }

  public function download($url, $path) {

    $basename = basename($url);
    $destination = "${path}/${basename}";

    $file = system_retrieve_file($url, "${destination}", FALSE, $this->fileSystem::EXISTS_REPLACE);

    if ($file) {
      $this->logger->notice("${url} saved to ${destination}");


      return $file;
    }
    else {
      $this->logger->error("Could not save ${url} to ${destination}");
      return false;
    }

    echo 3;

  }

  public function extract($file, $path) {
    $fileRealPath = $this->fileSystem->realpath($file);
    $zip = new Zip($fileRealPath);
    $zip->extract($path);
  }

  public function list($dir, $mask) {

return file_scan_directory($dir,$mask);




}


}

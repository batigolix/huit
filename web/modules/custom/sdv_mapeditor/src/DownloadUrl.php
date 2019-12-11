<?php

namespace Drupal\sdv_mapeditor;

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

  /**
   * Constructs a new DownloadUrl object.
   */
  public function __construct(FileSystemInterface $file_system, LoggerInterface $logger) {
    $this->fileSystem = $file_system;
    $this->logger = $logger;
  }

  public function createFolder($folder) {

    $path = "public://${folder}";

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
    if (system_retrieve_file($url, "${path}/test.html", FALSE, $this->fileSystem::EXISTS_REPLACE)) {
      $this->logger->notice("${url} saved to ${path}");
      return true;
    }
    else {
      $this->logger->error("Could not save ${url} to ${path}");
      return false;
    }

    echo 3;

  }

}

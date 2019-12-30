<?php

namespace Drupal\sdv_mapeditor;

/**
 * Interface DownloadUrlInterface.
 */
interface FileHandlerInterface {

  /**
   * Creates a folder in which a file can be downloaded.
   *
   * @param string $path
   *   Path to local folder that will be created.
   *
   * @return mixed
   *   Returns the path if succeeded, FALSE otherwise.
   */
  public function createFolder($path);

  /**
   * Downloads a file from a url to a local folder.
   *
   * @param string $url
   *   The URL of the file.
   * @param string $path
   *   The local folder where the file will be stored.
   *
   * @return bool|mixed
   *   Returns file if download succeeded, FALSE otherwise.
   */
  public function download($url, $path);

  /**
   * Extracts a file in a local folder.
   *
   * @param string $file
   *   The path of the file.
   * @param string $path
   *   The local folder where the file will be extracted.
   */
  public function extract($file, $path);

  /**
   * List the contents of folder.
   *
   * @param string $dir
   *   The path of the folder.
   * @param string $mask
   *   The preg_match() regular expression for files to be included.
   */
  public function list($dir, $mask);

  /**
   * Check if a file exists.
   *
   * @param string $path
   *   The path of the file.
   *
   * @return bool
   *   Returns true if the file exists, FALSE otherwise.
   */
  public function checkIfExists($path);

  /**
   * Check if a file exists.
   *
   * @param string $url
   *   The url of the resource.
   *
   * @return bool
   *   Returns true if the url is valid, FALSE otherwise.
   */
  public function checkUrl($url);

  /**
   * Provides list of WMS layers in a delimited format.
   *
   * @return string
   *   List of WMS layers in delimited format.
   */
  public function getWms();

}

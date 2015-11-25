<?php
chdir(__DIR__);

function sortString ($a, $b) {
  if ($a == '.' || $a == '..')
    return 1;
  if ($b == '.' || $b == '..')
    return 0;

  return intval($a) < intval($b);
}

class storage {
  static $baseDirectory = "images/";
  static $nextDirectory = 0;
  static $nextFile = 0;
  static $filesPerDirectory = 4096;

  static function init () {
    if (!is_dir(storage::$baseDirectory)) {
      mkdir(storage::$baseDirectory);
    }

    // Scan $baseDirectory to find which folders are in it
    // Get the highest number
    $folders = scandir(storage::$baseDirectory, SCANDIR_SORT_NONE);
    usort($folders, 'sortString');

    if (count($folders) && $folders[0] != '..' && $folders[0] != '.') {
      storage::$nextDirectory = intval($folders[0]);
      $files = scandir(storage::$baseDirectory.storage::$nextDirectory, SCANDIR_SORT_NONE);
      usort($files, 'sortString');

      if (count($files) && $files[0] != '..' && $files[0] != '.') {
        storage::$nextFile = intval($files[0])+1;
      }
    } else {
      mkdir(storage::$baseDirectory.storage::$nextDirectory);
    }
  }

  // TODO: This may have some concorrency problems...
  static function makeId () {
    $id = storage::$baseDirectory.storage::$nextDirectory.'/'.storage::$nextFile;

    touch($id);

    if (storage::$nextFile == storage::$filesPerDirectory-1) {
      storage::$nextDirectory++;
      storage::$nextFile = 0;
      mkdir(storage::$baseDirectory.storage::$nextDirectory);
    } else {
      storage::$nextFile++;
    }

    return $id;
  }

  static function writeFile ($id, $data, $position=0) {
    if (is_file($id)) {
      // Read/Write and do not erase contents
      $file = fopen($id, 'wb');

      fseek($file, $position);
      fwrite($file, $data);
      fclose($file);

      return true;
    } else {
      return false;
    }
  }
}

storage::init();

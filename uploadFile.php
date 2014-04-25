<?php

require_once('encodedFileHandler.php');

#TODO:
#Select Experiment No
#Select Phase No
#Select Session No
if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br>";
  }
else
  {
  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
  echo "Type: " . $_FILES["file"]["type"] . "<br>";
  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
  echo "Stored in: " . $_FILES["file"]["tmp_name"];
  $file = file_get_contents($_FILES["file"]["tmp_name"]);
  $instance = new converter();
  $instance->openFile($_FILES["file"]["tmp_name"]);
  $instance->printClass();
  }
?>
<?php
session_start();

if (!isset($_SESSION['user_id']))
  die();

include '../message.php';
include '../../db/storage.php';

$id = "";
$data = "";
$position = 0;

if (isset($_POST['id']) &&
    isset($_POST['data'])) {
  if (isset($_POST['position'])) {
    $position = intval($_POST['position']);
  }

  $id = $_POST['id'];
  $data = $_POST['data'];
} else
if (isset($_GET['id']) &&
    isset($_GET['data'])) {
  if (isset($_GET['position'])) {
    $position = intval($_GET['position']);
  }

  $id = $_GET['id'];
  $data = $_GET['data'];
} else {
  echo json_encode(api_error('Insufficient paramenters'));
}

$data = base64_decode($data);

if (storage::writeFile($id, $data, $position)) {
  echo json_encode(api_success('Sucesso'));
} else {
  echo json_encode(api_error('Error'));
}

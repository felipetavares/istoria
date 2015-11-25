<?php
session_start();

if (!isset($_SESSION['user_id']))
  die();

include '../message.php';
// Para guardar arquivos estáticos
include '../../db/storage.php';

echo json_encode(api_success(storage::makeId()));

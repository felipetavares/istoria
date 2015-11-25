<?php

// Create a variable for the connection
$C = new mysqli("localhost", "root", "12345678", "istoria");

// Check for errors
if (!isset($C)) {
  die();
}

if ($C->connect_errno) {
   echo "Erro na conexão com o banco (E" .
        $C->connect_errno . ") " . $C->connect_error;
   die();
}

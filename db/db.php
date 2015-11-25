<?php

/*
  Here are all the functions for interacting with the database.
  NO DATABASE MANIPULATION SHOULD OCCUR OUTSIDE THIS FILE!
*/

// Connect to the database
// after it we can use the
// variable $GLOBALS['C'] for interacting
// with the connection
include 'connect.php';

function print_query_error () {
  echo "Erro ao executar comando no banco (E" .
       $GLOBALS['C']->errno . ") " . $GLOBALS['C']->error;
}

// info table
include 'info.php';
// user table
include 'user.php';
// edit table
include 'edit.php';
// votes table
include 'votes.php';
// notifications table
include 'notifications.php';
// image storage
//include 'image.php';

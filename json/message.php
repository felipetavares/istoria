<?php

function api_error ($error) {
  return array(
    'more' => false,
    'message' => $error,
    'error' => 1
  );
}

function api_success ($data, $more=false) {
  return array(
    'more' => $more,
    'message' => $data,
    'error' => 0
  );
}

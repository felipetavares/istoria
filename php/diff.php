<?php
include 'finediff.php';

function readNumber ($from, &$position) {
  $result = '';

  while ($position < strlen($from) && is_numeric($from[$position])) {
    $result .= $from[$position++];
  }

  if ($result == '')
    return 1;

  return $result;
}

function readCount ($from, &$position, $how_many) {
  $position += $how_many;
  return substr($from, $position-$how_many, $how_many);
}

function renderHtml  ($from, $opcodes) {
  $position = 0;
  $oposition = 0;
  $html = '';

  while ($oposition < strlen($opcodes)) {
    $char = $opcodes[$oposition++];

    switch ($char) {
      case 'd':
        $num = readNumber($opcodes, $oposition);
        $delete = readCount($from, $position, $num);
        $html .= "<div class='ist-diff-delete'>".$delete."</div>";
      break;
      case 'i':
        $num = readNumber($opcodes, $oposition);
        $oposition++;
        $insert = readCount($opcodes, $oposition, $num);
        $html .= "<div class='ist-diff-insert'>".$insert."</div>";
      break;
      case 'c':
        $num = readNumber($opcodes, $oposition);
        $keep = readCount($from, $position, $num);
        $html .= "<div class='ist-diff-keep'>".$keep."</div>";
      break;
    }
  }

  return $html;
}


function createDiff ($from, $to) {
  $opcodes = FineDiff::getDiffOpcodes($from, $to, FineDiff::$wordGranularity);
  return renderHtml($from, $opcodes);
}

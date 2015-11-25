<?php
include 'finediff.php';
include 'Parsedown.php';
$parsedown = new Parsedown();

$from = $parsedown->text("**How** *hard* can it: be to compare two different phrases?");
$to = $parsedown->text("Given two different phrases, **how** *hard* can it: be to compare them?");

// Granularidade padrão é de caractere
$opcodes = FineDiff::getDiffOpcodes($from, $to, FineDiff::$wordGranularity);

// Recriando texto a partir do diff
$to = FineDiff::renderToTextFromOpcodes($from, $opcodes);

/*
echo $from."<br>";
echo $opcodes."<br>";

echo "<br>";
*/

function readNumber ($from, &$position) {
  $result = '';

  while (is_numeric($from[$position])) {
    $result .= $from[$position++];
  }

  return $result;
}

function readCount ($from, &$position, $how_many) {
  $position += $how_many;
  return substr($from, $position-$how_many, $how_many);
}

function renderHtml  ($from, $opcodes) {
  $position = 0;
  $oposition = 0;

  while ($oposition < strlen($opcodes)) {
    $char = $opcodes[$oposition++];

    switch ($char) {
      case 'd':
        $num = readNumber($opcodes, $oposition);
        $delete = readCount($from, $position, $num);
        echo "Deleting \"".$delete."\"<br>";
      break;
      case 'i':
        $num = readNumber($opcodes, $oposition);
        $oposition++;
        $insert = readCount($opcodes, $oposition, $num);
        echo "Inserting \"".$insert."\"<br>";
      break;
      case 'c':
        $num = readNumber($opcodes, $oposition);
        $keep = readCount($from, $position, $num);
        echo "Keeping \"".$keep."\"<br>";
      break;
    }
  }
}


renderHtml($from, $opcodes);

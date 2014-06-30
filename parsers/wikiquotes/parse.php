<?php

$people = array('kimi' => array('http://fi.wikiquote.org/wiki/Kimi_R%C3%A4ikk%C3%B6nen', '<li><i>', '</i>'),
  'antero' => array('http://fi.wikiquote.org/wiki/Antero_Mertaranta','<li>','</i>'),
  'matti' => array('http://fi.wikiquote.org/wiki/Matti_Nyk%C3%A4nen','<li>"','"'),
  'aleksis' => array('http://fi.wikiquote.org/wiki/Aleksis_Kivi','<li>"','"'),
  'andy' => array('http://fi.wikiquote.org/wiki/Andy_McCoy','<li>"','</li>'),
  'seppo' => array('http://fi.wikiquote.org/wiki/Seppo_R%C3%A4ty','<li><i>','</i>'));


foreach ($people as $file => $guide) {
$url = $guide[0];

  $data = file_get_contents($url);
// file_put_contents('data.txt', $data);
//$data = file_get_contents('data.txt');
//print $data;
  $ar = array();
  while (true) {
    preg_match('@(' . $guide[1] . ')(.*?)(' . $guide[2] . ')@s', $data, $temp);
    $data = str_replace($temp[1] . $temp[2], '', $data);
    $str = replaceOUML(strip_tags(trim($temp[2])));
    $str = preg_replace('@(\[)([0-9]*?)(\])@i','',$str);
    if (trim($str) == '') {
      break;
    }
    print $str . PHP_EOL;
    array_push($ar, '"' . trim($str) . '"');
  }
  sort($ar);
  file_put_contents("../../quotes/$file.json", json_encode($ar, JSON_PRETTY_PRINT));
}

function replaceOUML($str) {
  $map = array('&ouml;' => 'ö',
    '&Ouml;' => 'Ö',
    '&auml;' => 'ä',
    '&Auml;' => 'Ä',
    '&Uuml;' => 'Ü',
    '"' => '',
    '&uuml;' => 'ü');

  return str_replace(array_keys($map), array_values($map), $str);
}

?>

<?php

ini_set('display_errors', 0);

$people = array('kimi' => array('http://fi.wikiquote.org/wiki/Kimi_R%C3%A4ikk%C3%B6nen', '<li><i>', '</i>'),
    'antero' => array('http://fi.wikiquote.org/wiki/Antero_Mertaranta', '<li>', '</i>'),
    'matti' => array('http://fi.wikiquote.org/wiki/Matti_Nyk%C3%A4nen', '<li>"', '"'),
    'aleksis' => array('http://fi.wikiquote.org/wiki/Aleksis_Kivi', '<li>"', '"'),
    'andy' => array('http://fi.wikiquote.org/wiki/Andy_McCoy', '<li>"', '</li>'),
    'john' => array('http://math.ucr.edu/~cwalker66/John_Baez_Quotes.htm', '<font size="4">"?', '"?</font>'),
    'seppo' => array('http://fi.wikiquote.org/wiki/Seppo_R%C3%A4ty', '<li><i>', '</i>'));

$only = 'john';
$people = array($only => $people[$only]);

foreach ($people as $file => $guide) {
  $url = $guide[0];

  $data = file_get_contents($url);
  // print mb_detect_encoding($data, 'auto');  
  if ($file == 'john') {
    continue; // doesn't work    
    $data = iconv('iso-8859-1', 'utf-8', $data);
  }
// file_put_contents('data.txt', $data);
//$data = file_get_contents('data.txt');
//print $data;
  $ar = array();
  while (true) {
    preg_match('@(' . $guide[1] . ')(.*?)(' . $guide[2] . ')@s', $data, $temp);
    $data = str_replace($temp[1] . $temp[2], '', $data);
    $str = replaceOUML(strip_tags(trim($temp[2])));
    $str = preg_replace('@(\[)([0-9]*?)(\])@i', '', $str);
    // remove multiple white space
    $str = preg_replace('/\s+/', ' ', $str);
    
    if (trim($str) == '') {
      break;
    }
    $str = '"' . trim($str) . '"';
    $str = preg_replace('/""/', '"', $str);
    //print $str . PHP_EOL;
    array_push($ar, $str);
  }
  sort($ar);
  print_r($ar);
  file_put_contents("../../quotes/$file.json", json_encode($ar, JSON_PRETTY_PRINT));
  var_dump(error_get_last());
}

function replaceOUML($str) {
  $map = array('&ouml;' => 'ö',
      '&Ouml;' => 'Ö',
      '&auml;' => 'ä',
      '&Auml;' => 'Ä',
      '&Uuml;' => 'Ü',
      '&amp;' => '&',
      '&nbsp;' => ' ',      
      '&uuml;' => 'ü');
  return strip_tags(str_replace(array_keys($map), array_values($map), $str));
}

?>

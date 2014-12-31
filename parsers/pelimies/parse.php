<?php

$data = file_get_contents('http://www.saunalahti.fi/ja/pelimies_faq.html');
// file_put_contents('data.txt', $data);
//$data = file_get_contents('data.txt');
//print $data;
$ar = array();
$num = 0;
while (true) {
  preg_match('@(<p class="p1">\()([0-9].*?)(\))(.*?)(</p>)@s', $data, $temp);
  if (count($data) == 0){    
    break;
  }      
  $data = str_replace($temp[0],'', $data);
  $str = replaceOUML(strip_tags(trim($temp[4])));
  $str = preg_replace('@(\[)([0-9]*?)(\])@i', '', $str);
  if (trim($str) == '') {
    break;
  }
  $num++;
  print $num . ') ' . $str . PHP_EOL;
  array_push($ar, '"' . trim($str) . '"');
}
sort($ar);
file_put_contents("../../quotes/pelimies.json", json_encode($ar, JSON_PRETTY_PRINT));

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

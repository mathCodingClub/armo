<?php

$people = array('chuck1' => array('http://hikipedia.info/wiki/Hikisitaatit:Totuus_Universumin_Suurimmasta_Dorkasta..._Chuck_Norriksesta!', '<li>', '</li>'),
    'chuck2' => array('http://hikipedia.info/wiki/Hikisitaatit:Faktaa_Chuck_Norriksesta', '<li>', '</li>'));


foreach ($people as $file => $guide) {
  $url = $guide[0];

  $data = file_get_contents($url);
// file_put_contents('data.txt', $data);
//$data = file_get_contents('data.txt');
//print $data;
  $ar = array();
  $num = 0;
  while (true) {
    preg_match('@(' . $guide[1] . ')(.*?)(' . $guide[2] . ')@s', $data, $temp);
    $data = str_replace($temp[1] . $temp[2], '', $data);
    $str = replaceOUML(strip_tags(trim($temp[2])));
    $str = trim(preg_replace('@(\[)([0-9]*?)(\])@i', '', $str));
    if ($str == '') {
      break;
    }
    if ($str == 'Hikisitaatit'){
      break;
    }
    $num++;
    print $num . ') ' . $str . PHP_EOL;
    
    array_push($ar, '"' . $str . '"');
  }
  sort($ar);
  file_put_contents("../../quotes/$file.json", json_encode($ar, JSON_PRETTY_PRINT));
}

// Combine chuck norris
$data = array_merge(
    json_decode(file_get_contents("../../quotes/chuck1.json"),true),
    json_decode(file_get_contents("../../quotes/chuck2.json"),true)
    );
file_put_contents("../../quotes/chuck.json", json_encode($data, JSON_PRETTY_PRINT));
unlink("../../quotes/chuck1.json");
unlink("../../quotes/chuck2.json");

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

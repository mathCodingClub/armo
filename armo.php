<?php

namespace armo;

class armo {

  private $data;

  public function __construct($user) {
    $fileName = __DIR__ . '/quotes/' . $user . '.json';
    if (!file_exists($fileName)) {
      throw new Exception("Citations for '$user' do not exist.", 310);
    }
    $this->data = json_decode(file_get_contents($fileName), true);
  }

  // static methods
  static public function available($getAmounts = false) {
    $who = glob(__DIR__ . '/quotes/*.json');
    $av = array();
    foreach ($who as $val) {
      $name = str_replace(array(__DIR__ . '/quotes/', '.json'), '', $val);
      if ($getAmounts) {
        $temp = json_decode(file_get_contents($val), true);
        $name .= ' (' . count($temp) . ')';
      }
      array_push($av, $name);
    }
    return $av;
  }

  static function save($user, $quote) {
    $fileName = __DIR__ . '/quotes/' . $user . '.json';
    if (!file_exists($fileName)) {
      $data = array($quote);
    } else {
      $data = json_decode(file_get_contents($fileName), true);
      array_push($data, $quote);
    }
    file_put_contents($fileName, json_encode($data, JSON_PRETTY_PRINT));
  }

  // public methods
  public function get($ind = null) {
    if (is_null($ind) || $ind > $this->getMaxInd()) {
      return $this->data[rand(0, $this->getMaxInd())];
    }
    return $this->data[$ind];
  }

  public function getMaxInd() {
    return ($this->getSum() - 1);
  }

  public function getSum() {
    return count($this->data);
  }

}

?>
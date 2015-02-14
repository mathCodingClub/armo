<?php

namespace armo;

class armo {

  const databaseName = 'mcc_irc';
  const users = 'users';
  const messages = 'quotes';

  private $data;

  public function __construct($user) {
    $fileName = __DIR__ . '/quotes/' . $user . '.json';
    if (!file_exists($fileName)) {
      // try from database
      $db = self::getDB();
      $data = $db->getById(self::users, $username, 'username', 'id');
      if (!isset($data['id'])) {
        throw new \Exception("Citations for '$user' do not exist.", 310);
      }
      // now fetch quotes
      $query = $db->prepare('select message from ' . self::messages . ' where userId=:userId order by timestamp desc');
      $query->bindValue(':userId', $data['id'], \PDO::PARAM_INT);
      $this->data = $db->getDataList($query);
      if (count($this->data) == 0) {
        throw new \Exception("Citations for '$user' do not exist.", 310);
      }
    } else {
      $this->data = json_decode(file_get_contents($fileName), true);
    }
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

  static function save($username, $quote) {
    // This is about saving to database
    $db = self::getDB();
    $data = $db->getById(self::users, $username, 'username', 'id');
    if (!isset($data['id'])) {
      $db->insert(self::users, array('username' => $username));
      $userId = $db->getLastInsertId();
    } else {
      $userId = $data['id'];
    }
    $db->insert(self::messages, array('userId' => $userId,
        'message' => $quote));
    // OLD METHOD
    /*
      $fileName = __DIR__ . '/quotes/' . $user . '.json';
      if (!file_exists($fileName)) {
      $data = array($quote);
      } else {
      $data = json_decode(file_get_contents($fileName), true);
      array_push($data, $quote);
      }
      file_put_contents($fileName, json_encode($data, JSON_PRETTY_PRINT));
     */
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

  static private function getDB() {
    return new \database\sql(self::databaseName);
  }

}

?>
<?php

namespace armo;

class armo {

  // use also sql
  const databaseName = 'mcc_irc';
  const users = 'users';
  const messages = 'quotes';
  const extendToSql = true;

  private $data;
  
  private $isStatic;

  public function __construct($user) {
    $fileName = __DIR__ . '/quotes/' . $user . '.json';
    if (!file_exists($fileName) && !self::extendToSql) {
      throw new \Exception("Citations for '$user' do not exist.", 310);
    } elseif (!file_exists($fileName)) {
      // try from database
      $db = self::getDB();
      $data = $db->getById(self::users, $user, 'username', 'id');
      if (!isset($data['id'])) {
        throw new \Exception("Citations for '$user' do not exist.", 310);
      }
      // now fetch quotes
      $query = $db->prepare('select message, UNIX_TIMESTAMP(timestamp) as timestamp from ' . self::messages . ' where userId=:userId order by timestamp desc');
      $query->bindValue(':userId', $data['id'], \PDO::PARAM_INT);
      $this->data = $db->getData($query);
      $this->isStatic = false;
      if (count($this->data) == 0) {
        throw new \Exception("Citations for '$user' do not exist.", 310);
      }
    } else {
      $this->data = json_decode(file_get_contents($fileName), true);
      $this->isStatic = true;
    }
  }

  // static methods doesn't include now sql
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
    if (self::extendToSql) {
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
    } else {
      $fileName = __DIR__ . '/quotes/' . $username . '.json';
      if (!file_exists($fileName)) {
        $data = array($quote);
      } else {
        $data = json_decode(file_get_contents($fileName), true);
        array_push($data, $quote);
      }
      file_put_contents($fileName, json_encode($data, JSON_PRETTY_PRINT));
    }
  }

  // public methods
  public function get($ind = null) {
    if (is_null($ind) || $ind > $this->getMaxInd()) {
      $ind = rand(0, $this->getMaxInd());      
    }
    if ($this->isStatic){
      return $this->data[$ind];
    }
    else {
      return '"' . $this->data[$ind]['message'] . '" ' . date('D d.m.Y H:i:s',$this->data[$ind]['timestamp']);
    }
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
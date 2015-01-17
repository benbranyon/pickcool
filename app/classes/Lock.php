<?php
  
use NinjaMutex\Lock\MySqlLock;

class Lock
{
  static $mysql_lock = null;
  static function go($name, $callback) {
    if(!self::$mysql_lock)
    {
      self::$mysql_lock = new MySqlLock(
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        $_ENV['DB_HOST']
      );
    }

    Log::info("Acquiring {$name}");
    self::$mysql_lock->acquireLock($name);
    $ret = $callback();
    self::$mysql_lock->releaseLock($name);
    Log::info("Releasing {$name}");
    return $ret;
  }
}

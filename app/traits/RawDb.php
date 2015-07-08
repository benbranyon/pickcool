<?php
  
trait RawDb
{
  static function execute($sqls)
  {
    foreach($sqls as $sql)
    {
      $sql = self::sanitize_sql($sql);
      DB::statement($sql);
    }
  }
  
  static function sanitize_sql($sql)
  {
    $sql = preg_replace('/\s*\n\s*/', ' ', $sql);
    return $sql;
  }
}
<?php
  
trait Timestampable
{
  protected function getDateFormat()
  {
      return 'U';
  }
}
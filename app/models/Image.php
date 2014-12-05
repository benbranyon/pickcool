<?php
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Codesleeve\Stapler\ORM\EloquentTrait;

class Image  extends EloquentBase implements StaplerableInterface 
{
  use EloquentTrait;

  public function __construct(array $attributes = array()) {
      $this->hasAttachedFile('image', [
          'styles' => self::styles()
      ]);

      parent::__construct($attributes);
  }
  
  public function should_reprocess()
  {
    return $this->md5 != self::style_md5();
  }
  
  public static function style_md5()
  {
    return md5(json_encode(self::styles()));
  }
  
  public static function styles()
  {
    return [
      'large' => '640x640#',
      'featured' => '585x585#',
      'medium' => '400x400#',
      'thumb' => '180x180#',
      'admin' => '100x100#',
      'tiny' => '75x75#',
    ];
  }
  
  protected function getDateFormat() {
      return 'U';
  }
}

Image::saving(function($obj) {
  $obj->md5 = Image::style_md5();
});
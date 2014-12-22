<?php

class AssetController extends BaseController
{
  function image($id,$size) 
  {
    $image = Image::find($id);
    if(!$image)
    {
      App::abort(404);
    }

    $response = Response::make(
       File::get($image->image->path($size)), 
       200
    );
    $response->header(
      'Content-type',
      'image/jpeg'
    );
  
    $date = new DateTime();
    $date->modify('+1 year');
    $response->setExpires($date);
    $response->setMaxAge(60*60*24*365);
    $response->setTtl(60*60*24*365);

    return $response;
  }
  
  function get($etag, $type, $name)
  {
    switch($type)
    {
      case 'js':
        $js = [];
        $js[] = "var CP_DEBUG=".json_encode($_ENV['JS_DEBUG']==true);
        $js[] = "var BUGSNAG_ENABLED=".json_encode($_ENV['BUGSNAG_ENABLED']==true);
        $js[] = "var APP_VERSION=".json_encode($_ENV['ETAG']);
        $js[] = file_get_contents(storage_path().'/assets/js/app.js');
        $js = join(";\n", $js);
        $response = Response::make($js, 200);

        $response->header('Content-Type', "application/javascript");
        break;
      case 'css':
        $css = [];
        $css[] = file_get_contents(storage_path().'/assets/css/app.css');
        if($_ENV['BETA'])
        {
          $css[] = "
            body
            {
              background-color: rgb(255, 186, 155);
            }
          ";
        }
        $css = join("\n", $css);
        $response = Response::make($css, 200);

        $response->header('Content-Type', "text/css");
        break;
      case 'fonts':
        $data = file_get_contents(storage_path().'/assets/fonts/'.$name);
        $pathinfo = pathinfo($name);
        $map = [
          'woff'=>'application/font-woff',
          'ttf'=>'application/font-ttf',
          'eof'=>'application/vnd.ms-fontobject',
          'otf'=>'application/font-otf',
          'svg'=>'image/svg+xml',
        ];
        $response = Response::make($data, 200);

        $response->header('Content-Type', $map[$pathinfo['extension']]);
        break;
      default:
        $response = Response::make("Type {$type} not found", 404);
        $response->header('Content-Type', "text/plain");
    }
    $date = new DateTime();
    $date->modify('+1 year');
    $response->setExpires($date);
    $response->setMaxAge(60*60*24*365);
    $response->setTtl(60*60*24*365);
    return $response;
  }
  
}
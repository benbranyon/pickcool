<?php
  
Dotenv::load(dirname(__DIR__));
Dotenv::required([
  'APP_NAME',
  'RUN_MODE',
  'ENABLE_USER_PROFILE',
  'DB_HOST',
  'DB_USER',
  'DB_PASSWORD',
  'DB_CATALOG',
  'IS_MAIL_ENABLED',
  'MAIL_FROM_ADDRESS',
  'MAIL_FROM_NAME',
  'FACEBOOK_APP_ID',
  'FACEBOOK_SECRET',
  'IS_BETA',
  'IP_WHITELIST',
  'USE_SSL',
  'BUGSNAG_ENABLED',
  'BUGSNAG_API_KEY',
  
]);

function env($name, $default=null)
{
  if(!isset($_ENV[$name]) || !$_ENV[$name]) return $default;
  return $_ENV[$name];
}
<?php namespace Modl;

// We disable the cache limiter and start the native sessions (we don't want cookies)
session_cache_limiter(false);
session_start();
date_default_timezone_set('UTC');

use Slim;                                   // core framework
use Whoops\Run;                             // whoops makes debugging better
use Whoops\Handler\PrettyPageHandler;       //
use Whoops\Handler\JsonResponseHandler;     //


/* ----------------------------------------------------
 *
 * Bootstrap and path vars. Please do not use the "WEB_PATH" and "APP_PATH"
 * constants. They're injected into the config system and mutated from there.
 * They're only used for the initial bootstrap.
 *
 * -------------------------------------------------- */

define('WEB_PATH', dirname(__FILE__));
define('APP_PATH', WEB_PATH.'/..');

$config = require APP_PATH.'/app/config.php';
require_once APP_PATH.'/vendor/autoload.php';

if( $config['debug'] ) {
    $whoops = new Run;
    if( $app->request->isXhr() || $app->request->getMediaType() ) {
        $whoops->pushHandler(new JsonResponseHandler);
    } else {
        $whoops->pushHandler(new PrettyPageHandler);
    }
    $whoops->register();
}

/* ----------------------------------------------------
 *
 * Load the controllers, allow them to self-register
 * themselves, routes and middleware
 *
 * -------------------------------------------------- */

$dir = dir(APP_PATH.'/plugins');
while( ($entity = $dir->read()) ) {
    if( strpos($entity, '.') === 0 ) {
        continue;
    }

    $path = sprintf('%s/%s/%s.php', APP_PATH, $entity, $entity);
    include_once $path;
}
$dir->close();

/* ----------------------------------------------------
 *
 * Let's do this thing
 *
 * -------------------------------------------------- */

$app->run();

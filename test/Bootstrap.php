<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-admin for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-admin/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-admin/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Admin;

use Laminas\Loader\AutoloaderFactory;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading
 *
 * @subpackage UnitTest
 */
class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        static::initAutoloader();
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
            return;
        }

        $laminasPath = getenv('LAMINAS_PATH') ?: (defined('LAMINAS_PATH') ? LAMINAS_PATH : (is_dir($vendorPath . '/Laminas/library') ? $vendorPath . '/Laminas/library' : false));

        if (!$laminasPath) {
            throw new RuntimeException('Unable to load Laminas. Run `php composer.phar install` or define a LAMINAS_PATH environment variable.');
        }

        if (isset($loader)) {
            $loader->add('Laminas', $laminasPath . '/Laminas');
        } else {
            include $laminasPath . '/Laminas/Loader/AutoloaderFactory.php';
            AutoloaderFactory::factory(array(
                'Laminas\Loader\StandardAutoloader' => array(
                    'autoregister_laminas' => true,
                    'namespaces' => array(
                        'Laminas\ApiTools\Admin' => __DIR__ . '/../src/Laminas/ApiTools/Admin/',
                        __NAMESPACE__ => __DIR__ . '/LaminasTest/ApiTools/Admin/',
                        'Test' => __DIR__ . '/../vendor/Test/',
                    ),
                ),
            ));
        }
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}

Bootstrap::init();

<?php
namespace AgereTest\Progress;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $serviceManager;

    protected static $autoloader;

    public static function init()
    {
        $zf2ModulePaths = [dirname(dirname(__DIR__))];
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        static::initAutoloader();
        self::$autoloader->setPsr4(__NAMESPACE__ . '\\' , array(__DIR__));

        // use ModuleManager to load this module and it's dependencies
        $config = [
            'module_listener_options' => [
                'module_paths' => $zf2ModulePaths,
            ],
            'modules' => [
                'Agere\ZfcProgress',
                'Magere\Users',
            ],
        ];



        // if NEED use full project configuration
        #include $path . '/../init_autoloader.php';
        #self::$config = include $path . '/../config/application.config.php';
        #\Zend\Mvc\Application::init(self::$config);
        #self::$sm = self::getServiceManager(self::$config);

        // if DON'T NEED to use custom service manager
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        static::$serviceManager = $serviceManager;

        // if NEED to use custom service manager
        #$applicationConfig = require('../config/application.config.php.sample');
        #$config = ArrayUtils::merge($config, $applicationConfig);

        #$serviceManager = new ServiceManager(new ServiceManagerConfig($config['service_manager']));
        #$serviceManager->setService('ApplicationConfig', $config);
        #$serviceManager->get('ModuleManager')->loadModules();
        #static::$serviceManager = $serviceManager;

    }


    public static function chroot()
    {
        $rootPath = dirname(static::findParentPath('module'));
        chdir($rootPath);
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getAutoloader()
    {
        return static::$autoloader;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');
        if (file_exists($vendorPath . '/autoload.php')) {
            static::$autoloader = include $vendorPath . '/autoload.php';
        }
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }
}

Bootstrap::init();
Bootstrap::chroot();
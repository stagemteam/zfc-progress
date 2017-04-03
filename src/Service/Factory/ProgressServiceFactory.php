<?php
/**
 * Progress Status Service Factory
 *
 * @category Agere
 * @package Agere_Status
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 04.02.15 10:30
 */
namespace Agere\ZfcProgress\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\PluginManager;
use Agere\ZfcProgress\Service\ProgressService;
use Magere\Entity\Controller\Plugin\ModulePlugin;

class ProgressServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var PluginManager $cpm */
        $cpm = $container->get('ControllerPluginManager');
        //$userService = $container->get('UserService');
        //$user = $cpm->get('user');
        /** @var ModulePlugin $modulePlugin */
        $modulePlugin = $cpm->get('module');

        return (new ProgressService($modulePlugin));
    }
}
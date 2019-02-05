<?php
/**
 * Progress Status Service Factory
 *
 * @category Agere
 * @package Agere_Status
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 04.02.15 10:30
 */
namespace Stagem\ZfcProgress\Service\Factory;

use Popov\ZfcEntity\Helper\ModuleHelper;
use Psr\Container\ContainerInterface;
use Stagem\ZfcProgress\Service\ProgressService;

class ProgressServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ModuleHelper $moduleHelper */
        $moduleHelper = $container->get(ModuleHelper::class);

        return (new ProgressService($moduleHelper));
    }
}
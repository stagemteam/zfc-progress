<?php
/**
 * @category Agere
 * @package Agere_Progress
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 17.03.2017 16:54
 */
namespace AgereTest\Progress\Listener;

use Agere\ZfcProgress\Service\ContextInterface;
use Agere\ZfcProgress\Service\ProgressService;
use AgereTest\Progress\Fake\ProgressContextFake;
use DoctrineModuleTest\ServiceManagerTestCase;
use Mockery;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Exception;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;
use Agere\Current\Plugin\Current as CurrentPlugin;
use Agere\ZfcProgress\Listener\EditListener;

class EditListenerTest extends TestCase
{
    protected $eventManagerMock;

    protected function setUp()
    {
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function getConfig()
    {
        return include 'module/Agere/Progress/config/module.config.php';
    }

    public function testAttachEvent()
    {
        $listener = new EditListener();
        $listener->setConfig($config = $this->getConfig());
        $eventManagerMock = Mockery::mock(EventManagerInterface::class)
            ->shouldReceive('getSharedManager')
            ->andReturnSelf()
            ->getMock();

        foreach ($config['progress']['listeners'] as $id => $eventName) {
            $eventManagerMock->shouldReceive('attach')
                ->with($id, $eventName, [$listener, 'writeProgress'])
                ->once();
        }

        $listener->attach($eventManagerMock);
    }

    public function testStandardWorkflowWriteProgress()
    {
        $contextMock = Mockery::mock('Agere\Test\Model\Item');

        $eventMock = Mockery::mock(Event::class)
            ->shouldReceive('getParam')
            ->with('context')
            ->andReturn($contextMock)
            ->getMock();

        //$contextProgressMock = Mockery::mock('Agere\Test\Progress\TestContext')
        //$contextProgressMock = Mockery::mock('alias:' . ContextInterface::class)
        $contextProgressMock = $progressContext = new ProgressContextFake();
            /*->shouldReceive('setEvent')
            ->with($eventMock)
            ->getMock();*/

        $progressServiceMock = Mockery::mock(ProgressService::class)
            ->shouldReceive('writeProgress')
            ->with($contextProgressMock)
            ->getMock();

        $serviceManagerMock = Mockery::mock(ServiceManager::class)
            ->shouldReceive('get')
            ->with(ProgressService::class)
            ->andReturn($progressServiceMock)
            ->getMock();

        /** @var EditListener $listener */
        $listener = Mockery::mock(EditListener::class)->makePartial();
        $listener->shouldReceive('getProgressContext')
            ->andReturn($contextProgressMock);

        $listener->setServiceManager($serviceManagerMock);

        $listener->writeProgress($eventMock);
    }

    public function testWriteProgressMustReturnVoidWhenContextIsPassedButNotRegisteredInConfig()
    {
        $contextMock = Mockery::mock('Agere\Test\Model\Item');

        $event = Mockery::mock(Event::class);
        $event->shouldReceive('getParam')
            ->with('context')
            ->andReturn($contextMock);

        /** @var EditListener $listener */
        $listener = Mockery::mock(EditListener::class)->makePartial();
        $listener->shouldReceive('getProgressContext')
            ->andReturnFalse();

        $listener->writeProgress($event);
    }

    public function testWriteProgressMustReturnVoidWhenContextNotSet()
    {
        $event = Mockery::mock(Event::class);
        $event->shouldReceive('getParam')
            ->with('context')
            ->andReturnNull();

        $listener = new EditListener();
        $listener->writeProgress($event);
    }

    public function testStandardWorkflowOfGetProgressContext()
    {
        $namespace = 'Agere\Test';

        $contextMock = Mockery::mock('Agere\Test\Model\Item');
        $contextProgressMock = Mockery::mock('Agere\Test\Progress\TestContext');

        $currentPluginMock = Mockery::mock(CurrentPlugin::class)
            ->shouldReceive('currentModule')
            ->with($contextMock)
            ->andReturn($namespace)
            ->getMock();

        $serviceManagerMock = Mockery::mock(ServiceManager::class)
            ->shouldReceive('get')
            ->with('Agere\Test\Progress\TestContext')
            ->andReturn($contextProgressMock)
            ->getMock();

        $config = [
            'progress' => [
                $namespace => [
                    'context' => 'Agere\Test\Progress\TestContext',
                ],
            ],
        ];

        $listener = new EditListener();
        $listener->setConfig($config);
        $listener->setCurrentPlugin($currentPluginMock);
        $listener->setServiceManager($serviceManagerMock);

        $this->assertEquals($contextProgressMock, $listener->getProgressContext($contextMock));
    }

    public function testGetProgressContextMustReturnFalseIfContextConfigNotSetOrEmpty()
    {
        $contextMock = Mockery::mock('Agere\Test\Model\Item');
        $currentPluginMock = Mockery::mock(CurrentPlugin::class)
            ->shouldReceive('currentModule')
            ->with($contextMock)
            ->andReturn('Agere\Test')
            ->getMock();

        $listener = new EditListener();
        $listener->setConfig([]);
        $listener->setCurrentPlugin($currentPluginMock);

        $this->assertFalse($listener->getProgressContext($contextMock));
    }
}

<?php
/**
 * Progress Service Unit Test
 *
 * @category Agere
 * @package Agere_Progress
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 24.03.2017 14:44
 */
namespace AgereTest\Progress\Service;

use Agere\ZfcProgress\Model\Progress;
use Agere\ZfcProgress\Model\Repository\ProgressRepository;
use AgereTest\Progress\Fake\ModelStub;
use AgereTest\Progress\Fake\ProgressContextFake;
use Magere\Entity\Controller\Plugin\EntityPlugin;
use Magere\Entity\Controller\Plugin\ModulePlugin;
use Magere\Entity\Model\Entity;
use Magere\Entity\Model\Repository\EntityRepository;
use Mockery;
use Zend\Stdlib\Exception;
use Zend\ServiceManager\ServiceManager;
use DoctrineModuleTest\ServiceManagerTestCase;
use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;
use Agere\Current\Plugin\Current as CurrentPlugin;
use Agere\ZfcProgress\Listener\EditListener;
use Agere\ZfcProgress\Service\ProgressService;

class ProgressServiceTest extends TestCase
{
    /** @var ProgressService */
    protected $progressService;

    protected $modulePluginMock;

    protected $entityPluginMock;

    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = Mockery::mock('Doctrine\ORM\EntityManager', [
            //'getRepository' => $this->getRepositoryMock(),
            //'getClassMetadata' => (object) ['name' => CheckoutBooking::class],
            'persist' => null,
            'flush' => null,
            'contains' => true,
        ]);

        //$userMock = Mockery::mock('User');
        $this->entityPluginMock = Mockery::mock(EntityPlugin::class);
        $this->modulePluginMock = Mockery::mock(ModulePlugin::class)
            ->shouldReceive('getEntityPlugin')
            ->andReturn($this->entityPluginMock)
            ->getMock();

        $this->progressService = new ProgressService(/*$userMock, */$this->modulePluginMock);
        $this->progressService->setObjectManager($this->objectManagerMock);
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    /*public function getConfig()
    {
        return include 'module/Agere/Progress/config/module.config.php';
    }*/

    public function testGetProgress()
    {
        $this->entityPluginMock
            ->shouldReceive('setContext')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('getContext')
            ->andReturn('AgereTest\Progress')
            ->getMock();

        $this->prepareEntityRepository();

        $this->getProgressRepositoryMock()
            ->shouldReceive('getItemsProgress')
            ->andReturn($qb = new QueryBuilder($this->objectManagerMock))
            ->getMock();

        $this->assertInstanceOf(QueryBuilder::class, $this->progressService->getProgress(new ModelStub()));
    }

    public function testGetProgressByContext()
    {
        $this->entityPluginMock
            ->shouldReceive('setContext')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('getContext')
            ->andReturn('AgereTest\Progress')
            ->getMock();

        $this->prepareEntityRepository();

        $this->getProgressRepositoryMock()
            ->shouldReceive('getItemProgressByContext')
            ->andReturn($qb = new QueryBuilder($this->objectManagerMock))
            ->getMock();

        $this->assertInstanceOf(
            QueryBuilder::class,
            $this->progressService->getProgressByContext(new ModelStub(), new ProgressContextFake())
        );
    }

    public function testStandardWorkflowOfWriteProgress()
    {
        $contextMock = Mockery::mock('Magere\Entity\Model\Module');
        $entityMock = Mockery::mock('Magere\Entity\Model\Entity');

        $this->modulePluginMock->shouldReceive('setRealContext')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('getRealModule')
            ->andReturn($contextMock)
            ->getMock();

        $this->entityPluginMock->shouldReceive('setContext')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('getEntity')
            ->andReturn($entityMock)
            ->getMock();

        $repositoryMock = Mockery::mock('alias:' . ProgressRepository::class)
            ->shouldReceive('getClassName')
            ->andReturn(Progress::class)
            ->getMock();

        $this->objectManagerMock->shouldReceive('getRepository')
            ->with(Progress::class)
            ->andReturn($repositoryMock)
            ->getMock();


        $progressContext = new ProgressContextFake();
        $progress = $this->progressService->writeProgress($progressContext);

        $this->assertEquals($progressContext->getUser(), $progress->getUser());
        $this->assertEquals($progressContext->getExtra(), $progress->getExtra());
        $this->assertEquals($progressContext->getMessage(), $progress->getMessage());
        $this->assertEquals($progressContext->getItem()->getId(), $progress->getItemId());
        $this->assertEquals($contextMock, $progress->getContext());
        $this->assertEquals($entityMock, $progress->getEntity());
        $this->assertInstanceOf(\DateTime::class, $progress->getCreatedAt());
    }

    protected function prepareEntityRepository()
    {
        $entityMock = Mockery::mock('Magere\Entity\Model\Entity');
        $entityRepositoryMock = Mockery::mock('alias:' . EntityRepository::class)
            ->shouldReceive('findBy')
            ->with(['namespace' => ['AgereTest\Progress']])
            ->andReturn($entityMock)
            ->getMock();

        $this->objectManagerMock
            ->shouldReceive('getRepository')
            ->with(Entity::class)
            ->andReturn($entityRepositoryMock)
            ->getMock();

        return $entityRepositoryMock;
    }

    protected function getProgressRepositoryMock()
    {
        $progressRepositoryMock = Mockery::mock('alias:' . ProgressRepository::class);

        $this->objectManagerMock
            ->shouldReceive('getRepository')
            ->with(Progress::class)
            ->andReturn($progressRepositoryMock)
            ->getMock();

        return $progressRepositoryMock;
    }

}
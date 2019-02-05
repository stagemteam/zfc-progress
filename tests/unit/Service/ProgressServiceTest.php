<?php
/**
 * Progress Service Unit Test
 *
 * @category Stagem
 * @package Stagem_Progress
 * @author Popov Sergiy <popov@Stagem.com.ua>
 * @datetime: 24.03.2017 14:44
 */
namespace StagemTest\Progress\Service;

use Stagem\ZfcProgress\Model\Progress;
use Stagem\ZfcProgress\Model\Repository\ProgressRepository;
use StagemTest\Progress\Fake\ModelStub;
use StagemTest\Progress\Fake\ProgressContextFake;
use MStagem\Entity\Controller\Plugin\EntityPlugin;
use MStagem\Entity\Controller\Plugin\ModulePlugin;
use MStagem\Entity\Model\Entity;
use MStagem\Entity\Model\Repository\EntityRepository;
use Mockery;
use Zend\Stdlib\Exception;
use Zend\ServiceManager\ServiceManager;
use DoctrineModuleTest\ServiceManagerTestCase;
use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;
use Stagem\Current\Plugin\Current as CurrentPlugin;
use Stagem\ZfcProgress\Listener\EditListener;
use Stagem\ZfcProgress\Service\ProgressService;

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
        return include 'module/Stagem/Progress/config/module.config.php';
    }*/

    public function testGetProgress()
    {
        $this->entityPluginMock
            ->shouldReceive('setContext')
            ->andReturnSelf()
            ->getMock()

            ->shouldReceive('getContext')
            ->andReturn('StagemTest\Progress')
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
            ->andReturn('StagemTest\Progress')
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
        $contextMock = Mockery::mock('MStagem\Entity\Model\Module');
        $entityMock = Mockery::mock('MStagem\Entity\Model\Entity');

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
        $entityMock = Mockery::mock('MStagem\Entity\Model\Entity');
        $entityRepositoryMock = Mockery::mock('alias:' . EntityRepository::class)
            ->shouldReceive('findBy')
            ->with(['namespace' => ['StagemTest\Progress']])
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
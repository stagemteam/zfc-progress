<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_<package>
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Stagem\ZfcProgress\Action\Admin;

use Interop\Http\Server\RequestHandlerInterface;
use Popov\ZfcEntity\Service\EntityService;
use Popov\ZfcEntity\Service\ModuleService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stagem\ZfcAction\Page\AbstractAction;
use Stagem\ZfcProgress\Block\Grid\ProgressGrid;
use Stagem\ZfcProgress\Model\Progress;
use Stagem\ZfcProgress\Service\ProgressService;
use Zend\Router\RouteMatch;
use Zend\View\Model\ViewModel;

class IndexAction extends AbstractAction
{
    /**
     * @var ProgressService
     */
    protected $progressService;

    /**
     * @var ProgressGrid
     */
    protected $progressGrid;

    /**
     * @var EntityService
     */
    protected $entityService;

    /**
     * @var ModuleService
     */
    protected $moduleService;

    public function __construct(ProgressService $progressService, ProgressGrid $progressGrid,
        EntityService $entityService, ModuleService $moduleService)
    {
        $this->progressService = $progressService;
        $this->progressGrid = $progressGrid;
        $this->entityService = $entityService;
        $this->moduleService = $moduleService;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        return $handler->handle($request->withAttribute(ViewModel::class, $this->action($request)));
    }

    public function action(ServerRequestInterface $request)
    {
        $route = $request->getAttribute(RouteMatch::class);

        $contextMnemo = isset($request->getParsedBody()['filters']) ?
            $request->getParsedBody()['filters']['contextMnemo'] : null;

        $entityMnemo = isset($request->getParsedBody()['filters']) ?
            $request->getParsedBody()['filters']['entityMnemo'] : null;

        if ($contextMnemo != null && $entityMnemo != null) {
            $context = $this->moduleService->getRepository()->findOneBy(['mnemo' => $contextMnemo]);
            $entity = $this->entityService->getRepository()->findOneBy(['mnemo' => $entityMnemo]);

            $notifications = $this->progressService->getObjectManager()
                ->getRepository(Progress::class)
                ->getItemsProgressByContextEntity($context, $entity);
        } else {
            $notifications = $this->progressService
                ->getRepository()
                ->getAllProgress();
        }

        $this->progressGrid->init();
        $dataGrid = $this->progressGrid->getDataGrid();
        $dataGrid->setUrl($this->url()->fromRoute($route->getMatchedRouteName(), $route->getParams(), ['force_canonical' => true]));
        $dataGrid->setDataSource($notifications);
        $dataGrid->render();

        $dataGridVm = $dataGrid->getResponse();

        return $dataGridVm;
    }
}
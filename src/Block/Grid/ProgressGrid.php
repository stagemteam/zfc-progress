<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019 Serhii Popov
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

namespace Stagem\ZfcProgress\Block\Grid;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Popov\ZfcDataGrid\Block\AbstractGrid;
use Popov\ZfcEntity\Model\Entity;
use Popov\ZfcEntity\Model\Module;
use Stagem\ZfcProgress\Model\Progress;

class ProgressGrid extends AbstractGrid implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    protected $createButtonTitle = '';

    protected $backButtonTitle = '';

    protected $id = Progress::MNEMO;

    protected $entity = Progress::class;

    protected $moduleId = 'module';

    protected $entityId = Entity::MNEMO;

    public function init()
    {
        $grid = $this->getDataGrid();
        //$grid->setId($this->mnemo);
        $grid->setTitle('Price rules');
        $rendererOptions = $grid->getToolbarTemplateVariables();
        $rendererOptions['navGridEdit'] = true;
        $rendererOptions['navGridDel'] = true;
        //$rendererOptions['navGridSearch'] = true;
        //$rendererOptions['inlineNavEdit'] = true;
        $rendererOptions['inlineNavAdd'] = true;
        $rendererOptions['inlineNavCancel'] = true;
        $rendererOptions['inlineNavRefresh'] = true;
        $grid->setToolbarTemplateVariables($rendererOptions);

        $this->add([
            'name' => 'Select',
            'construct' => ['id', $this->id],
            'identity' => true,
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['id', $this->id],
            'label' => 'Id',
            'translation_enabled' => true,
            'width' => 1,
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['message', $this->id],
            'label' => 'Title',
            'translation_enabled' => true,
            'width' => 1,
            'renderer_parameters' => [
                ['editable', true, 'jqGrid'],
            ],
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['description', $this->id],
            'label' => 'Description',
            'width' => 3,
            'renderer_parameters' => [
                ['editable', true, 'jqGrid'],
                ['editrules', ['required' => true], 'jqGrid'],
            ],
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['extra', $this->id],
            'label' => 'Extra',
            'width' => 1,
            'renderer_parameters' => [
                ['editable', true, 'jqGrid'],
                ['editrules', ['required' => true], 'jqGrid'],
            ],
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['createdAt', $this->id],
            'label' => 'Date Create',
            'width' => 1,
            'renderer_parameters' => [
                ['editable', true, 'jqGrid'],
                ['editrules', ['required' => true], 'jqGrid'],
            ],
            'type' => [
                'name' => 'DateTime',
            ],
            'sortDefault' => [1, 'DESC'],
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['mnemo', $this->moduleId],
            'label' => 'Module',
            'width' => 1,
            'renderer_parameters' => [
                ['editable', true, 'jqGrid'],
                ['editrules', ['required' => true], 'jqGrid'],
            ],
            //'filter_default_value' => 'status',
            'filter_select_options' => [
                'options' => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class' => Module::class,
                    'identifier' => 'id',
                    'property' => 'mnemo',
                    'is_method' => false,
                    'option_attributes' => [
                        'multiple' => true,
                        'size' => 4,
                    ],
                ],
            ],
        ]);

        //$column->setFilterActive($filter->getDisplayColumnValue());

        return $grid;
    }
}
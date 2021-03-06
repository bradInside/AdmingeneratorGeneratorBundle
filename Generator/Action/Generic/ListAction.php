<?php

namespace Admingenerator\GeneratorBundle\Generator\Action\Generic;

use Admingenerator\GeneratorBundle\Builder\Admin\BaseBuilder;
use Admingenerator\GeneratorBundle\Generator\Action;

/**
 * This class describes generic List action
 * @author cedric Lombardot
 * @author Piotr Gołębiewski <loostro@gmail.com>
 */
class ListAction extends Action
{
    public function __construct($name, BaseBuilder $builder)
    {
        parent::__construct($name, 'generic');

        $this->setIcon('fa-list-alt');
        $this->setLabel('action.generic.list');
    }
}

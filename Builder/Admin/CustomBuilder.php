<?php

namespace Admingenerator\GeneratorBundle\Builder\Admin;

/**
 * This builder generates php for custom actions
 * @author Piotr Gołębiewski <loostro@gmail.com>
 */
class CustomBuilder extends BaseBuilder
{
    /**
     * (non-PHPdoc)
     * @see Admingenerator\GeneratorBundle\Builder.BaseBuilder::getYamlKey()
     */
    public function getYamlKey()
    {
        return 'custom';
    }

    /**
     * Return a list of action from list.object_actions
     * @return array
     */
    public function getObjectActions()
    {
        if (0 === count($this->object_actions)) {
            $this->findObjectActions();
        }

        return $this->object_actions;
    }
    
    protected function setUserObjectActionConfiguration(Action $action)
    {
        $options = $this->getVariable(sprintf('object_actions[%s]', $action->getName()),array(), true);

        if (null !== $options) {
            foreach ($options as $option => $value) {
                $action->setProperty($option, $value);
            }
        }
    }

    protected function addObjectAction(Action $action)
    {
        $this->object_actions[$action->getName()] = $action;
    }

    protected function findObjectActions()
    {
        foreach ($this->getVariable('object_actions') as $actionName => $actionParams) {
            $action = $this->findObjectAction($actionName);
            if(!$action) $action = new Action($actionName);

            $this->setUserObjectActionConfiguration($action);
            $this->addObjectAction($action);
        }
    }
}

<?php

namespace Admingenerator\GeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator as BaseBundleGenerator;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Generates an admin bundle.
 *
 * @author Cedric LOMBARDOT
 */
class BundleGenerator extends BaseBundleGenerator
{
    private $filesystem;
    private $skeletonDir;

    protected $generator;

    protected $prefix;

    protected $actions = array('New', 'List', 'Edit', 'Show', 'Actions');

    protected $forms = array('New', 'Filters', 'Edit');

    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        if (method_exists($this, 'setSkeletonDirs')) {
            $this->setSkeletonDirs($this->skeletonDir);
        }
    }

    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function generate($namespace, $bundle, $dir, $format, $structure, $generator, $modelName)
    {
        $dir .= '/'.strtr($namespace, '\\', '/');

        // Retrieves model folder depending of chosen ORM
        $modelFolder = '';
        switch ($generator) {
            case 'propel':
                $modelFolder = 'Model';
                break;
            case 'doctrine':
                $modelFolder = 'Entity';
                break;
            case 'doctrine_orm':
                $modelFolder = 'Document';
                break;
        }

        list( $namespace_prefix, $bundle_name) = explode('\\', $namespace, 2);
        $parameters = array(
            'namespace'        => $namespace,
            'bundle'           => $bundle,
            'generator'        => 'admingenerator.generator.'.$this->generator,
            'namespace_prefix' => $namespace_prefix,
            'bundle_name'      => $bundle_name,
            'model_folder'     => $modelFolder,
            'model_name'       => $modelName,
            'prefix'           => ucfirst($this->prefix),
        );

        if (!file_exists($dir.'/'.$bundle.'.php')) {
            $this->renderGeneratedFile('Bundle.php', $dir.'/'.$bundle.'.php', $parameters);
        }

        foreach ($this->actions as $action) {
            $parameters['action'] = $action;
            
            $controllerFile = $dir.'/Controller/'.($this->prefix ? ucfirst($this->prefix).'/' : '').$action.'Controller.php';
            $this->copyPreviousFile($controllerFile);
            $this->renderGeneratedFile(
                'DefaultController.php',
                $controllerFile,
                $parameters
            );

            $templateFile = $dir.'/Resources/views/'.ucfirst($this->prefix).$action.'/index.html.twig';
            $this->copyPreviousFile($templateFile);
            $this->renderGeneratedFile(
                'index.html.twig',
                $templateFile,
                $parameters
            );
        }

        foreach ($this->forms as $form) {
            $parameters['form'] = $form;
            
            $formFile = $dir.'/Form/Type/'.($this->prefix ? ucfirst($this->prefix).'/' : '').$form.'Type.php';
            $this->copyPreviousFile($formFile);
            $this->renderGeneratedFile(
                'DefaultType.php',
                $formFile,
                $parameters
            );
        }

        $generatorFile = $dir.'/Resources/config/'.($this->prefix ? ucfirst($this->prefix).'-' : '').'generator.yml';
        $this->copyPreviousFile($generatorFile);
        $this->renderGeneratedFile(
            'generator.yml',
            $generatorFile,
            $parameters
        );
    }

    protected function renderGeneratedFile($template, $target, array $parameters)
    {
        if (method_exists($this, 'setSkeletonDirs')) {
            $this->renderFile($template, $target, $parameters);
        } else {
            $this->renderFile($this->skeletonDir, $template, $target, $parameters);
        }
    }
    
    protected function copyPreviousFile($filename)
    {
        if(file_exists($filename)) {
            // Remove previous copy
            if(file_exists($filename.'~')) {
                unlink ($filename.'~');
            }

            // Create new copy
            rename($filename, $filename.'~');
        }
    }
}

<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid\Export;

/**
 * Export Abstract
 */
abstract class ExportAbstract {

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var string 
     */
    protected $name;
    
    /**
     *
     * @var \PedroTeixeira\Bundle\GridBundle\Grid\GridAbstract 
     */
    protected $grid;
    
    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(\Symfony\Component\DependencyInjection\Container $container, \PedroTeixeira\Bundle\GridBundle\Grid\GridAbstract $grid) {
        $this->grid = $grid;
        $this->container = $container;
    }

    public function getName() {
        return $this->name;
    }

    abstract public function process();
    
    abstract public function getFilename();
    
    abstract public function getDownloadResponse();
}

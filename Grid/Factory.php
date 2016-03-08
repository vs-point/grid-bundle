<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid;

/**
 * Grid Factory
 */
class Factory
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $gridClassName The name of the Grid descendant class that will be instantiated
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \PedroTeixeira\Bundle\GridBundle\Grid\GridAbstract
     *
     */
    public function createGrid($gridClassName, \Symfony\Component\HttpFoundation\Request $request)
    {
        $gridClass = new \ReflectionClass($gridClassName);

        /* @var \PedroTeixeira\Bundle\GridBundle\Grid\GridAbstract $grid */
        $grid = $gridClass->newInstance($this->container, $request);
        $grid->setupGrid();

        return $grid;
    }
}

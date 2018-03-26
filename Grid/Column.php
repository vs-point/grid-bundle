<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid;

/**
 * Grid Column
 */
class Column
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var string
     */
    protected $twig;

    /**
     * @var bool
     */
    protected $sortable = true;

    /**
     * @var string
     */
    protected $filterType = 'text';

    /**
     * @var \PedroTeixeira\Bundle\GridBundle\Grid\Filter\FilterAbstract
     */
    protected $filter;

    /**
     * @var string
     */
    protected $renderType = 'text';

    /**
     * @var bool
     */
    protected $exportOnly = false;

    /**
     * @var \PedroTeixeira\Bundle\GridBundle\Grid\Render\RenderAbstract
     */
    protected $render;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var bool
     */
    protected $hidden;

    /** @var string */
    protected $url;
    
    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param string                                           $name
     */
    public function __construct(\Symfony\Component\DependencyInjection\Container $container, $name = '')
    {
        $this->container = $container;
        $this->name = $name;
    }

    /**
     * @param string $name
     *
     * @return Column
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $field
     *
     * @return Column
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        if (empty($this->field)) {
            $this->field = uniqid();
        }

        return $this->field;
    }

    /**
     * @param string $index
     *
     * @return Column
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $twig
     *
     * @return Column
     */
    public function setTwig($twig)
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * @param bool $sortable
     *
     * @return Column
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @param string $filterType
     *
     * @return Column
     */
    public function setFilterType($filterType)
    {
        $this->filterType = $filterType;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilterType()
    {
        return $this->filterType;
    }

    /**
     * Return filter
     *
     * @todo Merge all the reflections in one single method
     *
     * @return \PedroTeixeira\Bundle\GridBundle\Grid\Filter\FilterAbstract|bool
     *
     * @throws \Exception
     */
    public function getFilter()
    {
        if ($this->filter) {
            return $this->filter;
        }

        $filterType = $this->getFilterType();

        if (!$filterType) {
            return false;
        }

        if (class_exists($filterType)) {
            $className = $filterType;
        } else {
            $className = str_replace('_', ' ', $filterType);
            $className = ucwords(strtolower($className));
            $className = str_replace(' ', '', $className);

            $className = 'PedroTeixeira\Bundle\GridBundle\Grid\Filter\\' . $className;
        }

        try {
            $reflection = new \ReflectionClass($className);

            $this->filter = $reflection->newInstance(
                $this->container
            );

            $this->filter->setIndex($this->getIndex());

            return $this->filter;

        } catch (\Exception $e) {
            throw new \Exception(
                sprintf('Grid column type "%s" doesn\'t exist', $filterType)
            );
        }
    }

    /**
     * @return null|string
     */
    public function renderFilter()
    {
        if ($this->getFilter()) {
            return $this->getFilter()->render();
        }

        return null;
    }

    /**
     * @param string $renderType
     *
     * @return Column
     */
    public function setRenderType($renderType)
    {
        $this->renderType = $renderType;

        return $this;
    }

    /**
     * @return string
     */
    public function getRenderType()
    {
        return $this->renderType;
    }

    /**
     * @param boolean $exportOnly
     *
     * @return Column
     */
    public function setExportOnly($exportOnly)
    {
        $this->exportOnly = $exportOnly;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getExportOnly()
    {
        return $this->exportOnly;
    }

    /**
     * Return render
     *
     * @todo Merge all the reflections in one single method
     *
     * @return \PedroTeixeira\Bundle\GridBundle\Grid\Render\RenderAbstract|bool
     *
     * @throws \Exception
     */
    public function getRender()
    {
        if ($this->render) {
            return $this->render;
        }

        $renderType = $this->getRenderType();

        if (!$renderType) {
            return false;
        }

        if (class_exists($renderType)) {
            $className = $renderType;
        } else {
            $className = str_replace('_', ' ', $renderType);
            $className = ucwords(strtolower($className));
            $className = str_replace(' ', '', $className);

            $className = 'PedroTeixeira\Bundle\GridBundle\Grid\Render\\' . $className;
        }

        try {
            $reflection = new \ReflectionClass($className);

            $this->render = $reflection->newInstance(
                $this->container
            );

            return $this->render;
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf('Grid render type "%s" doesn\'t exist', $renderType)
            );
        }
    }

    public function setHidden($bool): Column
    {
        $this->hidden = $bool;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): Column
    {
        $this->url = $url;

        return $this;
    }


}

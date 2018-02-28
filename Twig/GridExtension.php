<?php

namespace PedroTeixeira\Bundle\GridBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_Environment;
use Twig_Function;
use Twig_TemplateWrapper as Twig_Template;

use PedroTeixeira\Bundle\GridBundle\Grid\GridView;

/**
 * Grid Twig Extension
 */
class GridExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * @var Twig_Template[]
     */
    protected $templates;

    /**
     * @var string
     */
    const DEFAULT_TEMPLATE = 'PedroTeixeiraGridBundle::block.html.twig';

    /**
     * Construct
     *
     * @param ContainerInterface $container
     * @param Twig_Environment $environment
     */
    public function __construct(ContainerInterface $container, Twig_Environment $environment)
    {
        $this->container = $container;
        $this->environment = $environment;
    }

    /**
     * Template Loader
     *
     * @return Twig_Template[]
     *
     * @throws \Exception
     */
    protected function getTemplates()
    {
        if (empty($this->templates)) {
            $this->templates[] = $this->environment->load($this::DEFAULT_TEMPLATE);
        }

        return $this->templates;
    }

    /**
     * Render block
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function renderBlock($name, $parameters)
    {
        /** @var Twig_Template $template */
        foreach ($this->getTemplates() as $template) {
            if ($template->hasBlock($name)) {
                return $template->renderBlock($name, $parameters);
            }
        }

        throw new \InvalidArgumentException(sprintf('Block "%s" doesn\'t exist in grid template.', $name));
    }

    /**
     * Check if has block
     *
     * @param string $name
     *
     * @return bool
     */
    protected function hasBlock($name)
    {
        /** @var Twig_Template $template */
        foreach ($this->getTemplates() as $template) {
            if ($template->hasBlock($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('pedroteixeira_grid', [$this, 'renderGrid'], ['is_safe' => array('html')]),
            new Twig_Function('pedroteixeira_grid_js',  [$this, 'renderJsGrid'], ['is_safe' => array('html')]),
            new Twig_Function('pedroteixeira_grid_html',   [$this, 'renderHtmlGrid'], ['is_safe' => array('html')]),
        ];
    }

    /**
     * Render grid view
     *
     * @param GridView $gridView
     *
     * @return mixed
     */
    public function renderGrid(GridView $gridView)
    {
        if (!$gridView->getGrid()->isAjax()) {
            return $this->renderBlock(
                'grid',
                array(
                    'view' => $gridView
                )
            );
        }
    }

    /**
     * Render (only html) grid view
     *
     * @param GridView $gridView
     *
     * @return mixed
     */
    public function renderHtmlGrid(GridView $gridView)
    {
        if (!$gridView->getGrid()->isAjax()) {
            return $this->renderBlock(
                'grid_html',
                array(
                    'view' => $gridView
                )
            );
        }
    }

    /**
     * Render (only js) grid view
     *
     * @param GridView $gridView
     *
     * @return mixed
     */
    public function renderJsGrid(GridView $gridView)
    {
        if (!$gridView->getGrid()->isAjax()) {
            return $this->renderBlock(
                'grid_js',
                array(
                    'view' => $gridView
                )
            );
        }
    }
}

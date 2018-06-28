# vs-point/grid-bundle (Symfony3 fork)

[![Build Status](http://img.shields.io/travis/vs-point/grid-bundle.svg?style=flat)](https://travis-ci.org/vs-point/grid-bundle) [![Version](http://img.shields.io/packagist/v/vs-point/grid-bundle.svg?style=flat)](https://packagist.org/packages/vs-point/grid-bundle) [![Downloads](http://img.shields.io/packagist/dt/vs-point/grid-bundle.svg?style=flat)](https://packagist.org/packages/vs-point/grid-bundle) [![License](http://img.shields.io/packagist/l/vs-point/grid-bundle.svg?style=flat)](https://packagist.org/packages/vs-point/grid-bundle)

> Ajax grid for Symfony3

## Example

![](https://github.com/vs-point/grid-bundle/blob/master/Resources/doc/usage.gif)

*Using [Twitter Bootstrap](http://twitter.github.com/bootstrap/), [jQuery Bootstrap Datepicker](http://www.eyecon.ro/bootstrap-datepicker/) and fake data with [Faker](https://github.com/fzaninotto/faker).*

## Requirements

1. [Twitter Bootstrap](http://twitter.github.com/bootstrap/) (not mandatory)
    * If you choose to don't use Twitter Bootstrap, it'll be necessary to create your own style.
2. [jQuery Bootstrap Datepicker](http://www.eyecon.ro/bootstrap-datepicker/) (not mandatory)
    * If you choose to don't use Bootstrap Datepicker, please disable the datepicker as default in the configuration, "use_datepicker".

## Installation

1. **Add as a dependency in your composer file**

    ```json
    "require": {
        "vs-point/grid-bundle":"dev-master"
    }
    ```

2. **Add to your Kernel**

    ```php
    // application/ApplicationKernel.php
    public function registerBundles()
    {
        $bundles = array(
            new PedroTeixeira\Bundle\GridBundle\PedroTeixeiraGridBundle()
        );
     }
    ```

3. **Add to your assetics configuration**

    ```yml
    # application/config/config.yml
    assetic:
        bundles: [ PedroTeixeiraGridBundle ]
    ```

4. **Add assets to your layout**

    ```twig
    {% stylesheets
        '@PedroTeixeiraGridBundle/Resources/public/css/grid.css'
    %}
    <link href="{{ asset_url }}" type="text/css" rel="stylesheet" media="screen" />
    {% endstylesheets %}
    ```

    ```twig
    {% javascripts
        '@PedroTeixeiraGridBundle/Resources/public/js/grid.js'
    %}
    <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}
    ```

5. **(optional) Adjust configurations**

    ```yml
    # application/config/config.yml
    pedro_teixeira_grid:
        defaults:
            date:
                use_datepicker:     true
                date_format:        'dd/MM/yy'
                date_time_format:   'dd/MM/yy HH:mm:ss'
            pagination:
                limit:              20
            export:
                path:               '/tmp/'
    ```

    The configuration "use_datepicker" will set the input type as "text" and attach a jQuery plugin "datepicker()" to the filter.

## Create your grid

1. **Create the grid class**

    In your bundle, create a folder named "Grid" (or wathever namespace you want) and create your grid definition class.

    ```php
    <?php

    namespace PedroTeixeira\Bundle\TestBundle\Grid;

    use PedroTeixeira\Bundle\GridBundle\Grid\GridAbstract;
    use PedroTeixeira\Bundle\GridBundle\Grid\Export;

    /**
     * Test Grid
     */
    class TestGrid extends GridAbstract
    {
        /**
         * {@inheritdoc}
         */
        public function setupGrid()
        {
            $this->addColumn('Hidden Field')
                ->setField('hidden')
                ->setIndex('r.hidden')
                ->setExportOnly(true);

            $this->addColumn('ID')
                ->setField('id')
                ->setIndex('r.id')
                ->getFilter()
                    ->getOperator()
                        ->setComparisonType('equal');

            $this->addColumn('Created At')
                ->setField('createdAt')
                ->setIndex('r.createdAt')
                ->setFilterType('date_range')
                ->setRenderType('date');

            $this->addColumn('Name')
                ->setField('name')
                ->setIndex('r.name');

            $this->addColumn('Number')
                ->setField('number')
                ->setIndex('r.number')
                ->setFilterType('number_range');

            $this->addColumn('Options')
                ->setField('option')
                ->setIndex('r.options')
                ->setFilterType('select')
                ->getFilter()
                    ->setOptions(array(
                        'key' => 'value'
                    ));

            $this->addColumn('Action')
                ->setTwig('PedroTeixeiraTestBundle:Test:gridAction.html.twig')
                ->setFilterType(false);

            
            $this->addExportType(Export\ExportCsv::class);
        }
    }
    ```

2. **Use the grid factory in your controller**

    ```php
    <?php

    namespace PedroTeixeira\Bundle\TestBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

    use JMS\SecurityExtraBundle\Annotation\Secure;

    /**
     * Default controller
     */
    class DefaultController extends Controller
    {
        /**
         * @Route("/", name="test")
          * @Template()
          *
          * @return array
          */
        public function indexAction(Request $request)
        {
            /** @var \Doctrine\ORM\EntityRepository $repository */
            $repository = $this->getDoctrine()->getRepository('PedroTeixeiraTestBundle:TestEntity');
            $queryBuilder = $repository->createQueryBuilder('r');

            /** @var \PedroTeixeira\Bundle\TestBundle\Grid\TestGrid $grid */
            $grid = $this->get('pedroteixeira.grid')->createGrid('\PedroTeixeira\Bundle\TestBundle\Grid\TestGrid', $request);
            $grid->setQueryBuilder($queryBuilder);

            if ($grid->isResponseAnswer()) {
                return $grid->render();
            }

            return array(
                'grid'   => $grid->render()
            );
        }
    }
    ```

3. **Render in your template**

    ```twig
    {{ pedroteixeira_grid(grid) }}
    ````
    
   Or if you want to render the grid's html and the grid's js separately:
   
   ```twig
    {{ pedroteixeira_grid_html(grid) }}
    
    ...
    
    {{ pedroteixeira_grid_js(grid) }}
    ````
    
## Export to Excel or PDF

If you want add posibility for export to Excel or PDF, add to your setupGrid method these lines:

```
$this->addExportType(Export\ExportPdf::class);
$this->addExportType(Export\ExportXlsx::class);
```

Don't miss install external dependencies via composer:

```
composer install mpdf/mpdf
composer install phpoffice/phpspreadsheet
```

## Understanding

* [Grid](https://github.com/vs-point/grid-bundle/tree/master/Resources/doc/grid.md)
* [Column](https://github.com/vs-point/grid-bundle/tree/master/Resources/doc/column.md)
     * [Render](https://github.com/vs-point/grid-bundle/tree/master/Resources/doc/column/render.md)
     * [Filter](https://github.com/vs-point/grid-bundle/tree/master/Resources/doc/column/filter.md)

## Continuous integration

To execute locally the checks that are executed on Travis:

```bash
composer install
find ./Grid ./DependencyInjection ./Twig -name "*.php" -exec php -l {} \;
./bin/phpcs --extensions=php --standard=PSR2 ./Grid ./DependencyInjection ./Twig
```

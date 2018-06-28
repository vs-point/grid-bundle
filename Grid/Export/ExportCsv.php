<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid\Export;

use Symfony\Component\HttpFoundation\Response;

/**
 * Export CSV
 */
class ExportCsv extends ExportAbstract {

    public function __construct(\Symfony\Component\DependencyInjection\Container $container, \PedroTeixeira\Bundle\GridBundle\Grid\GridAbstract $grid) {
        $this->name = 'Export CSV';
        parent::__construct($container, $grid);
    }

    public function process() {
        $exportFile = $this->grid->getExportFileName();

        $fileHandler = fopen($exportFile, 'w');

        $columnsHeader = array();

        /** @var Column $column */
        foreach ($this->grid->getColumns() as $column) {
            if (!$column->getTwig()) {
                $columnsHeader[$column->getField()] = $column->getName();
            }
        }

        fputcsv($fileHandler, $columnsHeader);

        $data = $this->grid->getData();

        foreach ($data['rows'] as $row) {

            $rowContent = array();

            foreach ($row as $key => $column) {
                if (isset($columnsHeader[$key])) {
                    $rowContent[] = $column;
                }
            }

            fputcsv($fileHandler, $rowContent);
        }

        fclose($fileHandler);

        return array(
            'exportType' => $this->grid->getExportType(),
            'file_hash' => $this->grid->getFileHash(),
        );
    }

    function getFilename() {
        $exportPath = $this->container->getParameter('pedro_teixeira_grid.export.path');
        $exportFile = $exportPath . $this->grid->getName() . '_' . $this->grid->getFileHash() . '.csv';

        return $exportFile;
    }

    function getDownloadResponse() {
        $exportFile = $this->grid->getExportFileName();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($exportFile) . '"');
        $response->setContent(file_get_contents($exportFile));

        return $response;
    }

}

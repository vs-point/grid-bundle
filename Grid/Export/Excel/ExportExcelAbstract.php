<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid\Export\Excel;

use PedroTeixeira\Bundle\GridBundle\Grid\Export\ExportAbstract;

/**
 * Base Excel export method
 */
abstract class ExportExcelAbstract extends ExportAbstract {

    protected function processExcel() {
        
        $excel = new ExcelBuilder();

        $columnsHeader = [];
        /** @var Column $column */
        foreach ($this->grid->getColumns() as $column) {
            if (!$column->getTwig()) {
                $columnsHeader[$column->getField()] = $column->getName();
            }
        }
        $excel->addHeader($columnsHeader);

        $data = $this->grid->getData();
        foreach ($data['rows'] as $row) {

            $rowContent = [];

            foreach ($row as $key => $column) {
                if (isset($columnsHeader[$key])) {
                    $rowContent[] = $column;
                }
            }
            $excel->addRow($rowContent);
        }

        $excel->prepare();
        $excel->setName($this->grid->getName());
        
        return $excel;
    }

}

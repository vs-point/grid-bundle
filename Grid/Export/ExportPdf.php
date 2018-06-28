<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid\Export;

/**
 * Export PDF
 */
class ExportPdf extends Excel\ExportExcelAbstract {

    public function __construct(\Symfony\Component\DependencyInjection\Container $container, \PedroTeixeira\Bundle\GridBundle\Grid\GridAbstract $grid) {
        $this->name = 'Export PDF';
        parent::__construct($container, $grid);
    }

    public function process() {
        $exportFile = $this->grid->getExportFileName();

        $excel = $this->processExcel();
        $excel->saveToFilePdf($exportFile);
        
        return [
            'exportType' => $this->grid->getExportType(),
            'file_hash' => $this->grid->getFileHash(),
        ];
    }

    function getFilename() {
        $exportPath = $this->grid->container->getParameter('pedro_teixeira_grid.export.path');
        $exportFile = $exportPath . $this->grid->getName() . '_' . $this->grid->getFileHash() . '.pdf';

        return $exportFile;
    }

    function getDownloadResponse() {
        $exportFile = $this->grid->getExportFileName();

        $now = new \DateTime();
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($exportFile);
        $response->setContentDisposition(\Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT, $this->grid->getName() . '-' . $now->format('Y-m-d') . '.pdf');

        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

}

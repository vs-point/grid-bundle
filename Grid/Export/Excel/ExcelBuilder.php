<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid\Export\Excel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelBuilder {

    const HEADER_BACKGROUND = 'FFE0E7EA';
    const ROW_ODD_BACKGROUND = 'FFFFFFFF';
    const ROW_EVEN_BACKGROUND = 'FFF7F7F7';

    protected $spreadsheet;
    protected $sheet;
    protected $firstCol;
    protected $firstRow;
    protected $rowIndex;
    protected $lastCol;
    private $name;

    public function __construct() {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->sheet->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                ->setRowsToRepeatAtTopByStartAndEnd(1, 1);

        $this->firstCol = 'A';
        $this->firstRow = 1;

        $this->rowIndex = 1;
    }

    public function addHeader(array $values) {

        $this->lastCol = range('A', 'Z', count($values) - 1)[1];

        $row = $this->rowIndex;
        $col = $this->firstCol;
        foreach ($values as $value) {
            $this->sheet->setCellValue($col . $row, $value);
            $col++;
        }
        $this->rowIndex++;
    }

    public function addRow(array $values) {
        $row = $this->rowIndex;
        $col = $this->firstCol;
        foreach ($values as $value) {
            $this->sheet->setCellValue($col . $row, $value);
            $col++;
        }
        $this->rowIndex++;
    }

    public function prepare() {

        $this->setAutoWidth();

        $this->setColors();

        $this->sheet->getStyle($this->firstCol . $this->firstRow . ':' . $this->lastCol . $this->rowIndex)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        ;

        if ($this->name) {
            $this->sheet->setTitle($this->name);
        }
    }

    public function saveToFileXls($filename) {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($filename);
    }

    public function saveToFilePdf($filename) {
        $writer = new CustomMpdf($this->spreadsheet);
        $writer->setHeader($this->name);
        $writer->save($filename);
    }

    private function setAutoWidth(): void {
        $maxWidth = 120;
        $scale = 1.2;

        foreach (range($this->firstCol, $this->lastCol) as $col) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->sheet->calculateColumnWidths();

        foreach ($this->sheet->getColumnDimensions() as $colDim) {

            $colWidth = $colDim->getWidth() * $scale;

            $colDim->setAutoSize(false);

            if ($colWidth > $maxWidth) {
                $colDim->setWidth($maxWidth);
            } else {
                $colDim->setWidth($colWidth);
            }
        }
    }

    private function setColors() {
        // horizontal line
        $headerCells = $this->firstCol . $this->firstRow . ':' . $this->lastCol . $this->firstRow;
        
        // header color
        $this->sheet->getStyle($headerCells)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB(self::HEADER_BACKGROUND)
        ;
        $this->sheet->getStyle($headerCells)->getFont()->setBold(true);

        // rows color
        $index = 0;
        foreach (range($this->firstRow + 1, $this->rowIndex - 1) as $row) {

            $rowCells = $this->firstCol . $row . ':' . $this->lastCol . $row;

            if (++$index % 2 == 0) { // even
                $this->sheet->getStyle($rowCells)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB(self::ROW_EVEN_BACKGROUND)
                ;
            } else { // odd
                $this->sheet->getStyle($rowCells)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB(self::ROW_ODD_BACKGROUND)
                ;
            }
        }
    }

    public function setName($name) {
        $this->name = $name;
    }

}

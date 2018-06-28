<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid\Export\Excel;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class CustomMpdf extends Pdf\Mpdf {

    private $header;

    /**
     * @param string $header
     */
    public function setHeader(string $header): void {
        $this->header = $header;
    }

    public function generateHTMLHeader($pIncludeStyles = false) {
        return parent::generateHTMLHeader($pIncludeStyles) . '
<htmlpageheader name="header">
	<div style="text-align: center">' . $this->header . '</div>
</htmlpageheader>

<htmlpagefooter name="footer">
  <table width="100%">
        <tr>
            <td width="33%">{DATE j.m.Y H:i:s}</td>
            <td width="33%" align="right" style="text-align: right; ">{PAGENO}/{nbpg}</td>
        </tr>
    </table>
</htmlpagefooter>
	
<sethtmlpageheader name="header" page="all" value="on" show-this-page="1" />
<sethtmlpagefooter name="footer" page="all" value="on"  /> ';
    }

}

<?php

/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 18/09/17
 * Time: 15:46
 */
class PDFGenerator extends TCPDF
{
    public $pdf;
    //Page header
    public function Header() {

        $image_file = IMAGE_DIR.'/logo.jpg';
        $this->Image($image_file, 10, 10, 90, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, $this->header_string, 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
    static  public function CreateFromHtml($html, $title, $authorName)
    {
        $pdf = new PDFGenerator(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($authorName);
        $pdf->SetTitle($title);



        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 049', $title);

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


        $pdf->SetFont('helvetica', '', 10);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, 0, true, 0);
        $pdf->Output('example_049.pdf', 'I');
    }
}

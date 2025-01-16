
<?php

/**
 * @package     
 * @subpackage  mod AwForm
 **/

// No direct access.
defined('_JEXEC') or die;

require_once(JPATH_BASE.'/modules/mod_s7dform/library/tcpdf/tcpdf.php');

/********
 Classe Aw PDF.
 Desenvolvido por Carlos (IBS WEB)
********/

class awPdf {

	public function gPdf(&$params)
    {

        $dbTable = $params->get('db');
        $subject = $params->get('subject');
        $bodyPdf = $params->get('bodyPdf');
        $formToken = $_GET['awToken'];

        // Initialiase variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        //Data
        $datL = date('d/m/Y');
        
        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName($dbTable))
            ->where($db->quoteName('awToken') . ' = ' . $db->quote($formToken));

            ob_start();
        
        // Set the query and load the result.
        $db->setQuery($query);
        
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // set font
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTitle($subject);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // add a page
        $pdf->AddPage();

        /*************
         * Trabalhando com BodyPDF
        *************/
        preg_match_all('/{(.+)}/U', $bodyPdf, $bodyPdfArr);

        if(!empty($result))
        {
             /*Gerando vars*/
            parse_str(http_build_query($result[0]));
        }
       
        

        $iBodyTextArr = array();

        foreach($bodyPdfArr[1] as $iBody)
        {   
            $iBodyText = $$iBody;
            array_push($iBodyTextArr,$iBodyText);
        }

        $bodyPdf = str_replace($bodyPdfArr[0],$iBodyTextArr,$bodyPdf);

        // writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
        // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

        if(empty($result)):
            $html = 'Token Inválido';
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->SetMargins(10, 40, 10, true);
            $pdf->SetFont('helvetica', 900, 10);
        else:
            //$pdf->Image(JUri::base(true).'/images/pdfLogo.jpg', 10, 5, 40, 21, 'JPG', 'http://www.asbrapp.org.br', '', true, 350, '', false, false, 0, false, false, false);

        foreach($result as $k=> $item):
        // create some HTML content
        $html = $bodyPdf;

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        endforeach;
        endif;

        //$pdf->Cell(0,10,'Nome: juaskdf akosjf asfj oasfjaopsfjaskofjaskofj asodfjasopf jaosfj asofjasofjaosfjasfjasfjoasjfoas fjoasjf oasjf oasdfjoasfjasjf asfjopasfjpasjfoasjfoasjfoasj foasjf oasjdfkoasdfkoaskfaskofasofasfa',1,1,'LTRB',,'C',0);
        $pdf->Output();
        exit;
    }
}
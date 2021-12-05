<?php

define('FPDF_FONTPATH','../font/');
require_once('../fpdf.php');

class PDF extends FPDF {
    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';

    /******************************************* HTML Parser - BEGIN *******************************************/
    function WriteHTML($html) {
        // HTML parser
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,$e);
            }
            else
            {
                // Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extract attributes
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr) {
        // Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    function CloseTag($tag) {
        // Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }

    function SetStyle($tag, $enable) {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL, $txt) {
        // Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

    /******************************************** HTML Parser - END ********************************************/

    /******************************************* Header / Footer - BEGIN *****************************************/
    function Header() {
        // Logo
        $this->Image('logo.png',20, 10, 32);

        $this->SetFont('Verdana','B', 20);
        // Move to the right
        $this->Cell(40);
        $this->SetTextColor(192,0,0);         
        $this->Cell(56, 10,'BloodConnect',0,0);
        $this->SetTextColor(0,0,0);
        $this->Cell(56, 10,'Foundation',0,1);
        $this->SetFont('Verdana','', 12);
        $this->Cell(40);
        $this->Cell(125, 5, 'H-19, 1st Floor, Lajpat Nagar-2, New Delhi-110024', 0, 1);
        $this->Cell(40);
        $this->Cell(125, 5, 'Email: contact@bloodconnect.org | M: 9129824136', 0, 1);
        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-40);
        
        $about = 'BloodConnect is a social initiative started 6 years ago in IIT Delhi to solve the problem of blood shortage in India.';
        $about .= ' We have conducted over 300 blood donation camps, reached out to over 5 Lakh people, collecting over 30,000 units of blood and saving over 50,000 precious lives.';
        $abuot .= ' As a student-run initiative, we have chapters across prestigious institutions like IITs (Delhi, Kanpur, Ropar), NLUs (Delhi, Jodhpur), IIM (Kolkata, Lucknow, Kozhikode), Delhi University and Punjab University where our teams conduct awareness drives and blood donation camps at a regular frequency.';


        $more = 'For more information, visit: www.facebook.com/bloodconnect | www.bloodconnect.org';
        $this->SetFont('Verdana','', 9.5);
        $this->WriteHTML($about);
        $this->Ln(10);
        $this->SetFont('Verdana','', 9);
        $this->Cell(0,10,$more,0,0,'C');
    }
    /******************************************* Header / Footer - END *****************************************/
}

function get_input($var, $default) {
    return (isset($_REQUEST[$var]) ? (($_REQUEST[$var] != "") ? $_REQUEST[$var] : $default) : $default); 
}

// Editable Fields

$name = get_input('name', 'Hrishikesh');
$ref = get_input('ref','BC_Cert_2017_XYZ');
$date = get_input('date', date("d.m.Y"));
$gender = get_input('gender', 'M');
$bdc = get_input('bdc', 6);
$aws = get_input('aws', 2);

$from_date = get_input('from', date("M-Y"));
$till_date = get_input('to', date("M-Y"));

$pn0 = array('M' => "he", 'F' => 'she');
$pn1 = array('M' => "him", 'F' => 'her');
$pn2 = array('M' => "his", 'F' => 'her');

$extra = get_input('extra', 'Good Job!');

$certify = 'This is to certify that <b>' . $name . '</b> was associated with <b>BloodConnect</b> from ' . $from_date . ' till ' . $till_date . '. As a volunteer '. $pn0[$gender] .' handled '. $bdc .' Blood Donation Camps and '. $aws .' Awareness Sessions.';
$thank = 'We thank <b>' . $name . '</b> for ' . $pn2[$gender] . ' contribution towards a blood-sufficient India, and wish ' . $pn1[$gender] . ' luck in all ' . $pn2[$gender] . ' future endeavours.<br><br>';
$thank .= 'Thanking you,<br>';
$thank .= 'Yours sincerely,<br>';

// Signature Settings

$signature = 'signature.jpeg';
$stamp = 'stamp.jpg';

$signatory = 'Shubham Prakash<br>';
$signatory .= 'India President (Expansion)<br>';
$signatory .= 'BloodConnect<br>';
$signatory .= 'M: +91 9129824136 | E: <a href="mailto:shubham.prakash@bloodconnect.org">shubham.prakash@bloodconnect.org</a>';

$pdf = new PDF();
$pdf->AddFont('Verdana','','verdana.php');
$pdf->AddFont('Verdana','B','verdanab.php');

$pdf->SetMargins(20,12,20);
$pdf->AddPage();
$pdf->SetFont('Verdana','',11);
$pdf->Cell(0,10,'Ref No: ' . $ref, 0, 0, 'L');
$pdf->Cell(0,10,'Date: ' . $date, 0, 0, 'R');
$pdf->Ln(30);

$pdf->SetFont('Verdana','BU',12);
$pdf->Cell(0,10,'TO WHOMSOEVER IT MAY CONCERN', 0, 1,'C');
$pdf->Ln(10);

$pdf->SetFont('Verdana', '', 11);

//$pdf->Cell($pdf->GetStringWidth($certify), 10, $certify, 0, 0);
$pdf->WriteHTML($certify);
$pdf->Cell(1);
$pdf->WriteHTML($extra);
$pdf->Ln(10);
$pdf->WriteHTML($thank);
$pdf->Ln(10);

$x = $pdf->GetX();
$y = $pdf->GetY();

# $pdf->Image($signature,null,null,50);
# $pdf->Image($stamp, $x + 55, $y - 5, 32);

$pdf->WriteHTML($signatory);

$pdf->Output();

?>

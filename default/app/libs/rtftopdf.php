<?php 
class RtfToPdf{
	
	public static function leerdoc($documento,$ruta=null)
	{
		if (!$ruta) {
			$ruta = PUBLIC_PATH."files/pdf/";
		}
		//require('fpdf/fpdf.php');
		
		
		$reader = new RtfReader();
		$rtf = file_get_contents("$documento"); // or use a string
		$reader->Parse($rtf);

		$formatter = new RtfHtml();
		
	   	$pdf=new PDF_HTML();
	    $pdf->SetFont('Arial','',12);
	    $pdf->AddPage();
	    $text=$formatter->Format($reader->root);
	    if(ini_get('magic_quotes_gpc')=='1')
	        $text=stripslashes($text);
	    $pdf->WriteHTML($text);
	    $name = self::getDocName($documento);
	    $nombre_ruta = $ruta.$name.".pdf";
	    $pdf->Output($nombre_ruta,"F");
	    if (file_exists($nombre_ruta)) {
	    	return true;
	    }else{
	    	return false;
	    }
	}  
	public static function getDocName($url){
		$part1=explode(".rtf",$url);
		$part2=explode("/",$part1[0]);
		$name = $part2[count($part2)-1];
		return $name;
	}
}

 ?>
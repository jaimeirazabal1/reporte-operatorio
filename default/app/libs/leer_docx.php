<?php 
class LeerDocx{

	function read_file_docx($filename){

	    $striped_content = '';
	    $content = '';

	    if(!$filename || !file_exists($filename)) return false;

	    $zip = zip_open($filename);

	    if (!$zip || is_numeric($zip)) return false;

	    while ($zip_entry = zip_read($zip)) {

	        if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

	        if (zip_entry_name($zip_entry) != "word/document.xml") continue;

	        $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

	        zip_entry_close($zip_entry);
	    }// end while

	    zip_close($zip);

	    //echo $content;
	    //echo "<hr>";
	    //file_put_contents('1.xml', $content);

	    $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
	    $content = str_replace('</w:r></w:p>', "\r\n", $content);
	    $striped_content = strip_tags($content);

	    return $striped_content;
	}
	function read_doc_file($filename) {
     if(file_exists($filename))
    {
        if(($fh = fopen($filename, 'r')) !== false ) 
        {
           $headers = fread($fh, 0xA00);

           // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
           $n1 = ( ord($headers[0x21C]) - 1 );

           // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
           $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

           // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
           $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

           // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
           $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

           // Total length of text in the document
           $textLength = ($n1 + $n2 + $n3 + $n4);

           $extracted_plaintext = fread($fh, $textLength);

           // simple print character stream without new lines
           //echo $extracted_plaintext;

           // if you want to see your paragraphs in a new line, do this
           return nl2br($extracted_plaintext);
           // need more spacing after each paragraph use another nl2br
        }
    }   
    }

	
}

 ?>
<?php

// Include classes
// include_once('.php'); // Load the TinyButStrong template engine
// include_once('../tbs_plugin_opentbs.php'); // Load the OpenTBS plugin
Load::lib('opentbs/demo/tbs_class');
Load::lib('opentbs/tbs_plugin_opentbs');
// prevent from a PHP configuration problem when using mktime() and date()
if (version_compare(PHP_VERSION,'5.1.0')>=0) {
	if (ini_get('date.timezone')=='') {
		date_default_timezone_set('UTC');
	}
}

// Initialize the TBS instance
$TBS = new clsTinyButStrong; // new instance of TBS

$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load the OpenTBS plugin
// ------------------------------
// Prepare some data for the demo
// ------------------------------

// Retrieve the user name to display
$yourname = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$yourname = trim(''.$yourname);
if ($yourname=='') $yourname = "(no name)";

foreach ($_POST['fields'] as $key => $value) {
	${$key} = $value;
}
$TBS->VarRef = $_POST['fields'];



$data = array();
$data[] = array('rank'=> 'A', 'firstname'=>'Sandra' , 'name'=>'Hill'      , 'number'=>'1523d', 'score'=>200, 'email_1'=>'sh@tbs.com',  'email_2'=>'sandra@tbs.com',  'email_3'=>'s.hill@tbs.com');
$data[] = array('rank'=> 'A', 'firstname'=>'Roger'  , 'name'=>'Smith'     , 'number'=>'1234f', 'score'=>800, 'email_1'=>'rs@tbs.com',  'email_2'=>'robert@tbs.com',  'email_3'=>'r.smith@tbs.com' );
$data[] = array('rank'=> 'B', 'firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y', 'score'=>130, 'email_1'=>'wmc@tbs.com', 'email_2'=>'william@tbs.com', 'email_3'=>'w.m.dowell@tbs.com' );

// Other single data items
$x_num = 3152.456;
$x_pc = 0.2567;
$x_dt = mktime(13,0,0,2,15,2010);
$x_bt = true;
$x_bf = false;
$x_delete = 1;

// -----------------
// Load the template
// -----------------
if (file_exists(getcwd().$url)) {
	# code...
	$template = getcwd().$url;
}else{
	//echo getcwd().$url."<br>";
	Flash::error("La plantilla no existe");
	die;
}
$TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8); // Also merge some [onload] automatic fields (depends of the type of document).

// ----------------------
// Debug mode of the demo
// ----------------------
// if (isset($_POST['debug']) && ($_POST['debug']=='current')) $TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT, true); // Display the intented XML of the current sub-file, and exit.
// if (isset($_POST['debug']) && ($_POST['debug']=='info'))    $TBS->Plugin(OPENTBS_DEBUG_INFO, true); // Display information about the document, and exit.
// if (isset($_POST['debug']) && ($_POST['debug']=='show'))    $TBS->Plugin(OPENTBS_DEBUG_XML_SHOW); // Tells TBS to display information when the document is merged. No exit.

// --------------------------------------------
// Merging and other operations on the template
// --------------------------------------------

// Merge data in the body of the document

// $TBS->MergeBlock('a,b', $data);

// Merge data in colmuns
$data = array(
 array('date' => '2013-10-13', 'thin' => 156, 'heavy' => 128, 'total' => 284),
 array('date' => '2013-10-14', 'thin' => 233, 'heavy' =>  25, 'total' => 284),
 array('date' => '2013-10-15', 'thin' => 110, 'heavy' => 412, 'total' => 130),
 array('date' => '2013-10-16', 'thin' => 258, 'heavy' => 522, 'total' => 258),
);
// $TBS->MergeBlock('c', $data);


// Change chart series
$ChartNameOrNum = 'a nice chart'; // Title of the shape that embeds the chart
$SeriesNameOrNum = 'Series 2';
$NewValues = array( array('Category A','Category B','Category C','Category D'), array(3, 1.1, 4.0, 3.3) );
$NewLegend = "Updated series 2";
// $TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues, $NewLegend);

// Delete comments
// $TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

// -----------------
// Output the result
// -----------------

// Define the name of the output file
$save_as = (isset($_POST['save_as']) && (trim($_POST['save_as'])!=='') && ($_SERVER['SERVER_NAME']=='localhost')) ? trim($_POST['save_as']) : '';

$output_file_name = str_replace('.', '_'.date('Y-m-d').$save_as.'.', $template);
$save_as = 'files/upload/temporales/'.$output_file_name;


//var_dump($output_file_name);
if ($save_as==='') {
	// Output the result as a downloadable file (only streaming, no data saved in the server)
	$TBS->Show(OPENTBS_DOWNLOAD, $output_file_name); // Also merges all [onshow] automatic fields.
	// Be sure that no more output is done, otherwise the download file is corrupted with extra data.
	
	exit();
} else {
	// Output the result as a file on the server.
	$TBS->Show(OPENTBS_FILE, $output_file_name); // Also merges all [onshow] automatic fields.
	if (file_exists($output_file_name)) {
		$equipos = Load::model("usuario_equipo")->find("conditions: usuario_equipo.usuario_id='".Auth::get("id")."'","join: inner join equipo on equipo.id = usuario_equipo.equipo_id","columns: usuario_equipo.*, equipo.nombres_abreviado");
		$equipo = $equipos[0];
		$nombre_abreviado = str_replace(" ", "_", $equipo->nombres_abreviado);
		$fecha_hora = date("Y_m_s_H_i_s_");
		$historia = $_POST['fields']['hc'];
		
		$nombre=str_replace(" ", "_", $nombre_abreviado."-".$fecha_hora."-".$historia);
		
		//var_dump($output_file_name);

		//$url = Load::model('plantilla')->pasarAPdf($output_file_name,date('Y_m_d_H_i_s').'_exito');
		$url = Load::model('plantilla')->pasarAPdf($output_file_name,$nombre);
		//$url = "http://".$_SERVER['SERVER_NAME'].$url;
		$url = PUBLIC_PATH."archivos/get_".$url;
		$intervenciones = Load::model("intervenciones")->find("conditions: identificador='".$_POST['identificador']."' ");
		foreach ($intervenciones as $key => $value) {
			$value->url_pdf=$url;
			$value->update();
		}
		$to = Auth::get("email");
		$dep = Auth::get("departamento");
		$inst = Auth::get("institucion");
		$user = Auth::get("usuario");
		$mail = Mail::avisoNuevoReporte2($to,$user,$dep, $inst, $user,$url);
		$mail = Mail::avisoNuevoReporte2("jaimeirazabal1@gmail.com",$user,$dep, $inst, $user,$url);

		foreach ($equipos as $key => $value) {
			$equipo = Load::model("equipo")->find($value->equipo_id);
			$usuario = Load::model("usuario")->find($equipo->usuario_id);
			if(!Mail::avisoNuevoReporte2($usuario->email,$usuario->usuario, $usuario->departamento , $usuario->institucion, $user,$url)){
				Flash::error("Error mandando correo a $usuario->usuario");
			}
		}
		//die($url);
		$link = "<script>window.open('".$url."')</script>";


		echo $link;
	}
	// The script can continue.
	//exit("File [$output_file_name] has been created.");
}
 ?>

<?php
require_once "./class/Simpla.php";
$simpla = new Simpla();
$url = $simpla->request->get('file', 'string');
$action = $simpla->request->get('action', 'string');

if(!empty($url)){
	$file_obj = $simpla->files->get_file_by_url('http://seyarabata.com/'.$url);
	if($file_obj){
		if(file_exists($file_obj->file)){
			$filesize = filesize($file_obj->file);			
			if($filesize>134217720){
				$newUrl = str_replace('/home/rabata/www/','http://seyarabata.com/',$file_obj->file);				
				header('Location: '.$newUrl);
			}else{
				$ext = pathinfo($file_obj->file, PATHINFO_EXTENSION);
				switch( $ext ) {
					case "gif": $ctype="image/gif"; break;
					case "png": $ctype="image/png"; break;
					case "jpeg":
					case "jpg": $ctype="image/jpeg"; break;
					default: $ctype="";
				}
				if($action=='view' && !empty($ctype)){
					header('Content-type: ' . $ctype);
				}else{
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.basename($file_obj->file).'"');
				}				
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . $filesize);
				if (ob_get_level()) {
					ob_end_clean();
				}
				readfile($file_obj->file);
			}
			$log = array();
			if(isset($_SERVER['HTTP_REFERER'])){
				$ref_url = parse_url($_SERVER['HTTP_REFERER']);
				if($ref_url['host']!=$_SERVER['HTTP_HOST']){
					$log['http_referer'] = $_SERVER['HTTP_REFERER'];
					$log['http_referer_host'] = $ref_url['host'];
					if(isset($ref_url['path']))
						$log['http_referer_other'] = $ref_url['path'];
					else
						$log['http_referer_other'] = '';
					if(isset($ref_url['query'])){
						$log['http_referer_other'] .= '?'.$ref_url['query'];
					}		
				}
			}
			if(isset($_SERVER['HTTP_USER_AGENT']))
				$log['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$log['file_id'] = $file_obj->id;
			$log['ip'] = $simpla->logs->get_client_ip();
			$simpla->logs->add_log($log);
			$simpla->files->update_dl($file_obj->id);
			exit;
		}
	}
}
header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

<title>Файлшаринг</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.1.0/bootstrap.min.js"></script>
<style type="text/css">
	@import url('http://getbootstrap.com/dist/css/bootstrap.css');
 html, body, .container-table {
    height: 100%;
}
.container-table {
    display: table;
}
.vertical-center-row {
    display: table-cell;
    vertical-align: middle;
}
</style>
</head>
<body>
	<div class="container container-table">
	    <div class="row vertical-center-row">
	        <div class="text-center col-md-8 col-md-offset-2">
		        <h1>404</h1>
	        </div>
	    </div>
	</div>
</body>
</html>
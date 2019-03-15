<?php
session_start();
header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");
require_once "./class/Simpla.php";

$simpla = new Simpla();

$file_id = $simpla->request->get('id', 'string');
$file_url = $simpla->request->get('url', 'string');

if(isset($_COOKIE['user_id']))
	$user_id = $_COOKIE['user_id'];	
else
	error('invalid_user');

if(empty($file_id) || empty($file_url))
	error('empty_input');

$file = $simpla->files->get_file($file_id);

if(!$file)
	error('file_not_found');
if($file->url_id!=$file_id)
	error('file_id_not_found');

$i=1;
$ch_url = $file_url;
while(($file_by_url = $simpla->files->get_file_by_url('http://seyarabata.com/'.$ch_url)) && $file_by_url->id!=$file->id){
	$ch_url = $file_url.'_'.$i;
	$i++;
}
$simpla->files->update_url($file->id,'http://seyarabata.com/'.$ch_url);

$result['url'] = 'http://seyarabata.com/'.$ch_url;
$result['url_id'] = $ch_url;
print json_encode($result);

function error($message=''){
	$result['error'] = $message;
	print json_encode($result);
	exit;
}


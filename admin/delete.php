<?php
require_once('../class/Simpla.php');
$simpla = new Simpla();
$res = new stdClass;
$id = $simpla->request->post('id','integer');
// Если нажали оформить заказ
if(!empty($id)){
	$simpla->files->delete_file($id);
    $res->result = 'ok';
	
}
header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");
print json_encode($res);
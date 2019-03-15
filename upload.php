<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$targetDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "_tmp_";
$cleanupTargetDir = true;
$maxFileAge = 60 * 60; // Temp file age in seconds


// Create target dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir);
}

// Get a file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
} else {
	$fileName = uniqid("file_");
}

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);
$newFile = '';
$newUrl = '';
$uniqid = '';
if(isset($_COOKIE['user_id']))
	$user_id = $_COOKIE['user_id'];
else
	$user_id = '';
// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	$uniqid = uniqid();
	$newDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $uniqid;
	if (!file_exists($newDir)) {
		@mkdir($newDir);
	}
	$newUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $uniqid;
	$newFile = $newDir . DIRECTORY_SEPARATOR . $fileName;
	rename("{$filePath}.part", $newFile);
	require_once "./class/Simpla.php";
	$simpla = new Simpla();
	$file_obj = new stdClass();
	$file_obj->url_id = $uniqid;
	$file_obj->url = $newUrl;
	$file_obj->file = $newFile;
	$file_obj->file_size = filesize($newFile);
	$file_obj->user_id = $user_id;
	$simpla->files->add_file($file_obj);
}

// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : "'.$newUrl.'", "id" : "'.$uniqid.'"}');

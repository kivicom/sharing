<?php
session_start();
if(isset($_COOKIE['user_id']))
  $user_id = $_COOKIE['user_id'];
else
  $user_id = uniqid();
require_once "./class/Simpla.php";
$simpla = new Simpla();
$files = $simpla->files->get_files(array('order'=>'id','direction'=>'desc','user_id'=>$user_id));
?>
<?php if($files && is_array($files)): ?>
<?php foreach ($files as $file): ?> 
	<tr>
		<td><a href="<?php echo $file->url; ?>" target="_blank"><?php echo $file->url; ?></a></td>
		<td><?php echo Files::human_filesize($file->file_size); ?></td>
		<td><?php echo array_pop(explode(DIRECTORY_SEPARATOR, $file->file)); ?></td>
	</tr>
<?php endforeach; ?>
<?php endif ?>
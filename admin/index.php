<?php 
require_once "../class/Simpla.php";
$simpla = new Simpla();
$order = $simpla->request->get('order', 'string');
$direction = $simpla->request->get('direction', 'string');

$files = $simpla->files->get_files(array('order'=>$order,'direction'=>$direction));
//$date_format = 'd.m.Y H:i:s';
$date_format = 'd.m.Y';
function human_filesize($bytes, $decimals = 2) {
  $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . '&nbsp;' . @$size[$factor];
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <link rel="icon" href="/img/favicon.png">
    <title>Админка файлшаринга</title>

    <link href="http://getbootstrap.com/dist/css/bootstrap.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">          
          <a class="navbar-brand active" href="/admin">Админка файлшаринга</a>
        </div>
        <div class="navbar-header navbar-right top-nav">          
          <a class="navbar-brand nav navbar-right top-nav" href="/">Загрузить файл</a>
        </div>
      </div>
    </nav>

    <div class="container">

      <div class="starter-template">
      <?php if($files && is_array($files)): ?>
        <?php 
        $total_size = 0;
        foreach ($files as $file){
          $total_size += $file->file_size;
        } 
        ?> 
        <p>Всего файлов: <?php echo count($files); ?>, общий размер: <?php echo human_filesize($total_size); ?></p>
        <table class="table table-striped">
            <thead>
              <tr>
                
                <th
                  <?php if ($order == 'id' ): ?> class="selected"<?php endif ?>><a href="?order=id<?php if ($order == 'id' && $direction == 'desc'): ?>&direction=asc<?php elseif ($order == 'id'): ?>&direction=desc<?php endif ?>">ID&nbsp;<?php if ($order == 'id' && $direction == 'desc'): ?>&darr;<?php elseif ($order == 'id'): ?>&uarr;<?php endif ?></a></th>
                <th<?php if ($order == 'file' ): ?> class="selected"<?php endif ?>><a href="?order=file<?php if ($order == 'file' && $direction == 'desc'): ?>&direction=asc<?php elseif ($order == 'file'): ?>&direction=desc<?php endif ?>">Имя файла&nbsp;<?php if ($order == 'file' && $direction == 'desc'): ?>&darr;<?php elseif ($order == 'file'): ?>&uarr;<?php endif ?></a></th>
                <th<?php if ($order == 'file_size' ): ?> class="selected"<?php endif ?>><a href="?order=file_size<?php if ($order == 'file_size' && $direction == 'desc'): ?>&direction=asc<?php elseif ($order == 'file_size'): ?>&direction=desc<?php endif ?>">Размер&nbsp;<?php if ($order == 'file_size' && $direction == 'desc'): ?>&darr;<?php elseif ($order == 'file_size'): ?>&uarr;<?php endif ?></a></th>
                <th<?php if ($order == 'url' ): ?> class="selected"<?php endif ?>><a href="?order=url<?php if ($order == 'url' && $direction == 'desc'): ?>&direction=asc<?php elseif ($order == 'url'): ?>&direction=desc<?php endif ?>">URL&nbsp;<?php if ($order == 'url' && $direction == 'desc'): ?>&darr;<?php elseif ($order == 'url'): ?>&uarr;<?php endif ?></a></th>
                <th<?php if ($order == 'date' ): ?> class="selected"<?php endif ?>><a href="?order=date<?php if ($order == 'date' && $direction == 'desc'): ?>&direction=asc<?php elseif ($order == 'date'): ?>&direction=desc<?php endif ?>">Создание&nbsp;<?php if ($order == 'date' && $direction == 'desc'): ?>&darr;<?php elseif ($order == 'date'): ?>&uarr;<?php endif ?></a></th>
                <th<?php if ($order == 'dl_date' ): ?> class="selected"<?php endif ?>><a href="?order=dl_date<?php if ($order == 'dl_date' && $direction == 'desc'): ?>&direction=asc<?php elseif ($order == 'dl_date'): ?>&direction=desc<?php endif ?>">Загрузка&nbsp;<?php if ($order == 'dl_date' && $direction == 'desc'): ?>&darr;<?php elseif ($order == 'dl_date'): ?>&uarr;<?php endif ?></a></th>
                <th<?php if ($order == 'dl_count' ): ?> class="selected"<?php endif ?>><a href="?order=dl_count<?php if ($order == 'dl_count' && $direction == 'desc'): ?>&direction=asc<?php elseif ($order == 'dl_count'): ?>&direction=desc<?php endif ?>">Загрузок&nbsp;<?php if ($order == 'dl_count' && $direction == 'desc'): ?>&darr;<?php elseif ($order == 'dl_count'): ?>&uarr;<?php endif ?></a></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($files as $file): ?>            
              <tr id="<?php echo $file->id; ?>">
                <td><?php echo $file->id; ?></td>
                <td class="filename"><?php echo array_pop(explode(DIRECTORY_SEPARATOR, $file->file)); ?></td>
                <td class="filesize"><?php echo human_filesize($file->file_size); ?></td>
                <td><a href="<?php echo $file->url; ?>"><?php echo $file->url; ?></a></td>
                <td><?php echo date($date_format,strtotime($file->date)); ?></td>
                <td><?php echo $file->dl_count ? date($date_format,strtotime($file->dl_date)) : '-'; ?></td>
                <td><?php echo $file->dl_count; ?></td>
                <td><button type="button" class="btn btn-default" title="Удалить" data-toggle="modal" data-target="#confirm-delete" data-fid="<?php echo $file->id; ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif ?>
      </div>

    </div><!-- /.container -->

    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Подтвердите удаление</h4>
                </div>
            
                <div class="modal-body">
                    <p class="debug-url"></p>
                    <p>Вы уверены, что хотите удалить файл?</p>                    
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <a class="btn btn-danger btn-ok">Удалить</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <script>
       $('#confirm-delete').on('show.bs.modal', function(e) {            
            var btn = $(e.relatedTarget);
            var fileName = btn.parents('tr').find('.filename').text();
            var fileSize = btn.parents('tr').find('.filesize').text();
            $(this).find('.btn-ok').data('fid', btn.data('fid'));
            $('.debug-url').html('Удаляемый файл: <strong>' + fileName + '</strong> (' + fileSize + ')');
        });
        $('#confirm-delete').on('click', '.btn-ok',function(e) {
          var fid = $(this).data('fid');
          $('#confirm-delete').modal('hide');
          $.ajax({
          type: "POST",
          url: "delete.php",
          data: {id: fid},
          dataType: 'json',
          success: function(data){
            if(data.result=='ok'){
                $('#'+fid).hide('slow');
            }
          }
          });          
        });
    </script>
  </body>
</html>
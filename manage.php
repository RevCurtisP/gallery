<html>
  <head>
    <title>Resh Gallery Management</title>
  </head>
  <body>
  User: <?exec('whoami');?><br/>
<?
  require_once('resh.php');
  define('THUMB_HEIGHT', 200);
  $action = getRequestValue('action');
  if ($action == 'build_thumbnails'):
    echo "Building Thumbnails<br>";
    buildThumbnails('');
  else:
?>
  <a href="<?=SCRIPT?>?action=build_thumbnails">Build Thumbnails</a>
<?
  endif;
?>
  </body>
</html>
<?
  function buildThumbnails($dir)
  {
    $fileArray = glob("$dir*", GLOB_MARK);
    foreach($fileArray as $fileName):
      if (substr($fileName, -1) == '/'):
        //file is directory
        buildThumbnails($fileName);
      else:
        $path = pathinfo($fileName);
        $file = $path['filename'];
        $ext = strtolower($path['extension']);
        if (strpos(' gif jpg jpeg png ', " $ext ") === FALSE) continue;
        if (substr($file, -6) == '_thumb') continue;
        $thumbFile = $file . "_thumb.jpg";
        $image = new Imagick($fileName);
        $image->thumbNailImage(0,THUMB_HEIGHT);
        $image->writeImage($thumbFile);
        echo "Created thumbnail $thumbFile<br>";
      endif;
    endforeach;
  }
    
?>

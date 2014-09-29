<?php
require "gallery.php";
if (array_key_exists('file', $_REQUEST)):
  $file = $_REQUEST['file'];
  $base = rtrim($file,"/");
  $back = pathinfo("/$base", PATHINFO_DIRNAME);
  if ($back && $back !='/') $back .= '/';
  if ($base == $file):
    $image = Image::from_file($file);
    $image_src = $back . $image->file;
    $collection = null;
  else:
    $name = pathinfo($base, PATHINFO_FILENAME);
    $collection = Collection::from_file($file . $name);
  endif;
else:
  $back = null;
  $collection = Collection::from_file('gallery');
endif;
$item = $collection ? $collection : $image;
$title = htmlspecialchars($item->title);
$logo = $item->logo;
$description = htmlspecialchars($item->description);
$details = htmlspecialchars($item->details);
?>
<!DOCTYPE html> 
<html lang="en"> 
  <head> 
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width", initial-scale="1"/>
    <title><?=$title;?></title>
    <meta name="description" content="<?=$description;?>" />
    <link rel="stylesheet" type="text/css" href="/style.css" />
  </head> 
  <body> 
    <div id="header">
<?if ($back):?>
      <a id="back" href="<?=$back?>">Up One Level</a>
<?endif;?>
      <h1 id="title">
<?if ($logo):?>
        <img id="logo" src="<?=$logo;?>" alt="<?=$title;?>"/>
<?else:?>
        <?=$title;?>
<?endif;?>
      </h1>
      <h2 id="description">
        <?=$description;?>
      </h2>
    </div>
    <div id="main">
<?if ($collection):?>
      <div id="contents">
<?  $contents=$collection->contents;
    if (is_array($contents)):
      foreach ($contents as $item):
        $item_url = $item->url;
        $item_title = htmlspecialchars($item->title);
        $item_description = htmlspecialchars($item->description);
        $item_thumbnail = $item->thumbnail('not_available_thumb.jpg');?>
        <a href="/<?=$item_url;?>" class="item">
          <img class="item_thumb" src="/<?=$item_thumbnail;?>" alt="" title="<?=$item_description;?>">
          <h3 class="item_title"><?=$item_title;?></h3>
          <!-- <?=get_class($item);?> -->
        </a>
<?    endforeach;
     else:?>
        <h3>Collection is Empty</h3>
<?   endif;?>  
      </div>
<?else:?>
      <div id="item">
        <img class="image" src="<?=$image_src;?>" alt="" title="<?=$description?>">
      </div>
<?endif;?>
      <h3 id="details">
        <?=$details;?>
      </h3>
    </div>    
    <div id="footer">
    </div>
  </body> 
</html>


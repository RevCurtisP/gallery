<?php

/* Base class for Gallery Objects */
abstract class Item
{
  public function __construct($title)
  {
    $this->url = null;
    $this->filename = null;
    $this->title = $title;
    $this->logo = null;
    $this->description = null;
    $this->details = null;
  }  

  public function load_ini_file()
  {
    $ini = parse_ini_file($this->ini_file);      
    $this->load_array($ini);
  }

  /* Load object properties from array */
  public function load_array($props)
  {
    if (array_key_exists('FILE', $props))
      $this->file = $props['FILE'];
    if (array_key_exists('TITLE', $props))
      $this->title = $props['TITLE'];
    if (array_key_exists('LOGO', $props))
      $this->logo = $props['LOGO'];
    if (array_key_exists('DESCRIPTION', $props))
      $this->description = $props['DESCRIPTION'];
    if (array_key_exists('DETAILS', $props))
      $this->details = $props['DETAILS'];           
  }
    
  /* Build and return thumbnail filename */
  public function thumbnail($default=null)
  {
    $filebase = $this->filebase();
    if ($filebase):
      $thumbfile = $filebase . '_thumb.jpg';
    else:
      $thumbfile = null;
    endif;
    echo "<!-- Thumbfile: $thumbfile -->";
    if ($thumbfile and file_exists($thumbfile)):
      $thumbnail = $thumbfile;
    else:
      $thumbnail = $default;
    endif;
    return $thumbnail;
  }

  /* Strip Extension from filename */
  private function filebase()
  {
      $filename = $this->filename;
      $lastdot = strrpos($filename, '.');
      if ($lastdot > 0):
        return substr($filename, 0, $lastdot);
      else:
        return $filename;
      endif;
  }
}

class Collection extends Item
{

  public function __construct($title='Unnamed Collection')
  {
    parent::__construct($title);
    $this->contents = array();
  }

  /* Create Collection object from Config and CSV files */
  public static function from_file($filename, $load_lst_file=true)
  {
    $collection = new Collection();
    $collection->set_files($filename);
    $collection->load_ini_file();
    if ($load_lst_file) $collection->load_lst_file();
    return $collection;
  }

  public function set_files($filename)
  {
    $this->url = pathinfo("$filename", PATHINFO_DIRNAME) . '/';
    $this->filename = $filename;
    $this->title = pathinfo($filename, PATHINFO_FILENAME);
    $this->description = $this->title . ' collection';
    $this->ini_file = $filename . '.ini';
    $this->lst_file = $filename . '.lst';
  }
  
  public function load_lst_file()
  {
      $lst = file($this->lst_file);
      if ($lst):
        foreach ($lst as $line):
          $line = trim($line);
          if ($line):
            $file = rtrim($line, '/');
            if ($file == $line):
              $path = $this->url . $file;
              $this->contents[] = Image::from_file($path);
            else:
              $path = $this->url . $line . $file;
              $this->contents[] = Collection::from_file($path, false);
            endif;
          endif;
        endforeach;
      endif;
  }
  
} //class Collection

class Image extends Item
{
  public function __construct($title='Unnamed Image')
  {
    parent::__construct($title);
    $this->file = null;
  }

  /* Create Collection object from Config and CSV files */
  public static function from_file($filename)
  {
    $image = new Image();
    $image->set_files($filename);
    $image->load_ini_file();
    return $image;
  }  

  public function set_files($filename)
  {
    $this->url = $filename;
    $this->filename = $filename;
    $this->title = pathinfo($filename, PATHINFO_FILENAME);
    $this->description = $this->title;
    $this->ini_file = $filename . '.ini';
  }

}

?>

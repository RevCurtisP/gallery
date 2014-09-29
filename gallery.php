<?php

/* A collection may contain both sub-collections and items. */
class Collection
{

  public function __construct($title='Unnamed Collection')
  {
    $this->filename = null;
    $this->title = $title;
    $this->logo = null;
    $this->description = null;
    $this->details = null;
    $this->contents = array();
  }

  /* Create Collection object from array */
  public function from_array($props)
  {
    $collection = new Collection();
    $collection->load_array($props);
    return $collection;
  }
    
  /* Create Collection object from Config and CSV files */
  public function from_files($filebase)
  {
    $collection = new Collection();
    $collection->load_cfg_file($filebase . '.cfg');
    $collection->load_csv_file($filebase . '.csv');
    return $collection;
  }
  
  public function load_cfg_file($cfg_file)
  {
    $cfg = parse_ini_file($cfg_file);      
    $this->load_array($cfg);
  }
  
  public function load_csv_file($csv_file)
  {
    if ($fp = fopen($csv_file, 'r')):
      $headers = fgetcsv($fp);
      $fields = sizeof($headers);
      while ($line = fgetcsv($fp)):
        $row = array();
        for ($i=0; $i<$fields; $i++):
          $row[$headers[$i]] = $line[$i];
        endfor;
        $this->contents[] = Collection::from_array($row);
      endwhile;
      fclose($fp);
    endif;
  }
  
  /* Load object properties from array */
  public function load_array($props)
  {
    $collection = new Collection();
    if (array_key_exists('FILENAME', $props))
      $this->filename = $props['FILENAME'];
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
  public function thumbnail()
  {
    $filebase = $this->filename;
    if ($filebase):
      $filename = $filebase . '/' . $filebase . '_thumb.jpg';
    else:
      $filename = null;
    endif;
    if ($filename and file_exists($filename)):
      $thumbnail = $filename;
    else:
      $thumbnail = null;
    endif;
    return $thumbnail;
  }

}


?>

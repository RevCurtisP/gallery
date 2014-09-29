<?php

/**
 * Container for a configuration file
 * 
 * @property string cfg_file config file used to create object
 * @property array cfg_array array of key-value pairs read from config file
 */
class Config
{
  public function __construct($filespec)
  {
    if (!file_exists($filespec)):
      throw new Exception("Configuration file $filespec not found");
    elseif (!is_readable($filespec)):
      throw new Exception("Configuration file $filespec not readable");      
    else:
      $this->cfg_file = $filespec;
      $this->cfg_array = parse_ini_file($filespec);
    endif;
  }    

  /**
   * Return configuration value or default value if undefined
   * 
   * @param $key key text
   * @param $default value to return if no value is defined
   */  
  public function get_value($key, $default=null)
  {
    if (array_key_exists($key, $this->cfg_array)):
      return $this->cfg_array[$key];
    else:
      return $default;
    endif;
  }
  
  /**
   * Return configuration value, or throw exception if undefined
   * @param $key - key text
   */
  public function require_value($key)
  {
    $value = $this->get_value($key, '');
    if ($value === ''):
      throw new Exception("Parameter $key undefined in file " . $this->cfg_file);
    else:
      return $this->cfg_array[$key];
    endif;
  }
} //class Config

/**
 * Container for a CSV file
 * 
 * @property string csv_file csv file used to create object
 * @property array headers array of header values read from CSV file file
 * @property array rows array of rows read from CSV file
 */
class CSV
{
  /**
   * Create a new CSV container from the specified file.
   * 
   * @Param: $filespec - CSV file to create container from
   * 
   */
  public function __construct($filespec)
  {
    if (!file_exists($filespec)):
      throw new Exception("CSV file $filespec not found");
    elseif (!is_readable($filespec)):
      throw new Exception("CSV file $filespec not readable");      
    else:
      $this->file = $filespec;
      $this->field_count = 0;
      $this->headers = null;
      $this->row_count = 0;
      $this->rows = array();
      if ($fp = fopen($filespec, 'r')):
        $this->headers = fgetcsv($fp);
        $this->field_count = sizeof($this->headers);
        while ($row = fgetcsv($fp)) $this->rows[] = $row;
        fclose($fp);
      else:
        throw new Exception("CSV file $filespec could not be opened");      
      endif;
      $this->row_count = sizeof($this->rows);
    endif;
  } 

  /*
   * Return contents of specified row and column,
   * or default value if row or column do not exist
   * 
   * @param int $row_index index of row
   * @param string $column_name name of column
   */  
  public function get_field($row_index, $column_name, $default=null)
  {
    $field = $default;
    if (array_key_exists($row_index, $this->rows)):
      $column_index = array_search($column_name, $this->headers);
      if ($column_index !== false):
        $field = $this->rows[$row_index][$column_index];
      endif;
    endif;
    return $field;
  }

} //class CSV

/**
 * A collection may contain both sub-collections and items.
 */
class Collection
{
  public function __construct($param)
  {
    if (is_string($param)):
      $this->load_cfg_file($param . '.cfg');
      $this->load_csv_file($param . '.csv');
    elseif (is_array($param)):
    else:
      throw new Exception('Invalid parameter');
    endif;
  }
  
  public function load_cfg_file($cfg_file)
  {
    $cfg = new Config($cfg_file);
    $this->title = $cfg->require_value('TITLE');
    $this->logo = $cfg->get_value('LOGO');
    $this->description = $cfg->require_value('DESCRIPTION');
    $this->details = $cfg->get_value('DETAILS');
  }
  
  public function load_csv_file($csv_file)
  {
    $csv = new CSV($csv_file);
    return;
    $contents = array();
    foreach ($csv->rows as $row):
      print_r($row);
      $pathinfo = pathinfo($row['FILENAME']);
      if (array_key_exists('extension', $pathinfo)):
        $contents[] = new Item($row);
      else:
        $contents[] = new Collection($row);
      endif;
    endforeach;
  }
  
}


?>

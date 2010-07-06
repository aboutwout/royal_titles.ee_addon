<?php

/**
* @package ExpressionEngine
* @author Wouter Vervloet
* @copyright  Copyright (c) 2010, Baseworks
* @license    http://creativecommons.org/licenses/by-sa/3.0/
* 
* This work is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported.
* To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/
* or send a letter to Creative Commons, 171 Second Street, Suite 300,
* San Francisco, California, 94105, USA.
* 
*/

if ( ! defined('EXT')) { exit('Invalid file request'); }

class Royal_titles
{
  public $settings            = array();
  
  public $name                = 'Royal Titles';
  public $version             = 0.6;
  public $description         = "Enable EE global variables for the default weblog title setting.";
  public $settings_exist      = 'n';
  public $docs_url            = '';
  
	// -------------------------------
	// Constructor
	// -------------------------------
	function Royal_titles($settings='')
	{
	  $this->__construct($settings);
	}
	
	function __construct($settings='')
	{	  
		$this->settings = $settings;	
	}
	// END Super_titles
	
	function parse_preferences($data = array())
	{
	  
	  global $LOC, $PREFS;
	  
	  $val = $data['default_entry_title'];
	  
	  if( ! $val ) return $data;
	  	  	  
	  foreach($data as $key => $var)
		{
			$val = str_replace(LD.$key.RD, $var, $val); 
		}

 		if (strpos($val, LD.'current_time') !== FALSE && preg_match_all("/".LD."current_time\s+format=([\"\'])([^\\1]*?)\\1".RD."/", $val, $matches))
 		{	
  		for ($j = 0; $j < count($matches['0']); $j++)
  		{				
  			$val = preg_replace("/".preg_quote($matches['0'][$j], '/')."/", $LOC->decode_date($matches['2'][$j], $LOC->now), $val, 1);				
  		}
	  }

	  
	  $data['default_entry_title'] = $val;

	  return $data;
	}

	// --------------------------------
	//  Activate Extension
	// --------------------------------
	function activate_extension()
	{
	  
	  global $DB;

    $sql = array();

    // hooks array
    $hooks = array(
      'publish_form_weblog_preferences' => 'parse_preferences'
    );

    // insert hooks and methods
    foreach ($hooks AS $hook => $method)
    {
      // data to insert
      $data = array(
        'class'		=> get_class($this),
        'method'	=> $method,
        'hook'		=> $hook,
        'priority'	=> 1,
        'version'	=> $this->version,
        'enabled'	=> 'y',
        'settings'	=> ''
      );

      // insert in database
      $sql[] = $DB->insert_string('exp_extensions', $data);
    }

    // run all sql queries
    foreach ($sql as $query) {
      $DB->query($query);
    }

    return true;
	}
	// END activate_extension
	 
	 
	// --------------------------------
	//  Update Extension
	// --------------------------------  
	function update_extension($current='')
	{
	  global $DB;
		
    if ($current == '' OR $current == $this->version)
    {
      return FALSE;
    }
    
    if($current < $this->version) { }

    // init data array
    $data = array();

    // Add version to data array
    $data['version'] = $this->version;    

    // Update records using data array
    $sql = $DB->update_string('exp_extensions', $data, "class = '".get_class($this)."'");
    $DB->query($sql);
  }
  // END update_extension

	// --------------------------------
	//  Disable Extension
	// --------------------------------
	function disable_extension()
	{	
	  global $DB;
	
    // Delete records
    $DB->query("DELETE FROM exp_extensions WHERE class = '".get_class($this)."'");
  }
  // END disable_extension

	 
}
// END CLASS
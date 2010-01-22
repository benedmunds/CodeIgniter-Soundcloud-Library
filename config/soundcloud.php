<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Soundcloud API Config
* 
* Authors:  Ben Edmunds            |    Plasticated
*           ben.edmunds@gmail.com       http://plasticated.com
*           @benedmunds
*           
* Created:  10.20.2009 
* 
* Description:  Config file for Soundcloud API
* 
* Requirements: You need to register your app with SoundCloud for your key 
*               and secret at http://soundcloud.com/settings/applications/new
* 
*/

$config['soundcloud_key']          = '2iUWgjPoDFKed0ykO7HYQ'; 
$config['soundcloud_secret']       = '4voqXyCdpD47sKeLYmrgsAsqlGE1FPpHNh1sUVOJQ'; 
$config['soundcloud_callback_url'] = 'http://yoursite.com/soundclouddemo/'; 
$config['soundcloud_tmp_path']     = $_SERVER['DOCUMENT_ROOT'].'path/to/writable/folder/'; 
	
?>
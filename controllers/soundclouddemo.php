<?php
/**
* Name:  SoundCloud Demo
* 
* Authors:  Ben Edmunds             |   Plasticated
*           ben.edmunds@gmail.com       http://plasticated.com
*           @benedmunds
*           
* Created:  10.20.2009 
* 
* Description:  Demo of the CodeIgniter SoundCloud API Library.
* 
* Requirements: You must set uri_protocol to PATH_INFO and add a ? to your permitted_uri_chars in 
*               your config.php in order for OAuth to read the $_GET variables
* 
*/

class Soundclouddemo extends Controller {

	function __construct() {
		parent::__construct();	
		
		//load the input and session libraries - this can be removed if they are auto-loaded
		$this->load->library(Array('input','session'));
		
		//start the session
		session_start();
		
		//rebuild the $_GET array because CI unsets it
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		
		//Load settings from config file
		$this->load->config('soundcloud');
		$this->data['consumer_key']    = $this->config->item('soundcloud_key');
		$this->data['consumer_secret'] = $this->config->item('soundcloud_secret');
		$this->data['callback_url']    = $this->config->item('soundcloud_callback_url');
		$this->data['tmp_path']        = $this->config->item('soundcloud_tmp_path');
		
		// Variables used for verifying the status of the "OAuth dance"
		$this->oauth_token                = (isset($_GET['oauth_verifier']))                 ? $_GET['oauth_verifier']                 : ((isset($_SESSION['oauth_access_token'])) ? $_SESSION['oauth_access_token'] : NULL);
		$this->oauth_request_token        = (isset($_SESSION['oauth_request_token']))        ? $_SESSION['oauth_request_token']        : NULL;
		$this->oauth_request_token_secret = (isset($_SESSION['oauth_request_token_secret'])) ? $_SESSION['oauth_request_token_secret'] : NULL;
		
		/**
		 * Handle the OAuth 'Dance' and load the soundcloud library with appropriate settings
		 */
		// Retreive access tokens if missing
	    if (!isset($_SESSION['oauth_access_token']) && !isset($_SESSION['oauth_access_token_secret']) && isset($this->oauth_token) && isset($this->oauth_request_token) && isset($this->oauth_request_token_secret)) {  //need the access token
	        $soundcloud_config = Array('consumer_key'       => $this->data['consumer_key'], 
	        						   'consumer_secret'    => $this->data['consumer_secret'], 
	        						   'oauth_token'        => $_SESSION['oauth_request_token'], 
	        						   'oauth_token_secret' => $_SESSION['oauth_request_token_secret']);
			$this->load->library('soundcloud',$soundcloud_config);
			
			//Get the access token
	        $token                                 = $this->soundcloud->get_access_token($this->oauth_token);
	        $_SESSION['oauth_access_token']        = $token['oauth_token'];
	        $_SESSION['oauth_access_token_secret'] = $token['oauth_token_secret'];
	    }
		elseif (isset($this->oauth_token) && isset($this->oauth_request_token) && isset($this->oauth_request_token_secret)) { //token is complete
		    // Construct a fully authicated connection with SoundCloud
		    $soundcloud_config = Array('consumer_key'       => $this->data['consumer_key'],
		                               'consumer_secret'    => $this->data['consumer_secret'], 
		                               'oauth_token'        => $_SESSION['oauth_access_token'], 
		                               'oauth_token_secret' => $_SESSION['oauth_access_token_secret']);
			$this->load->library('soundcloud',$soundcloud_config);	
		}  
		else { //This is the first step
		   $soundcloud_config = Array('consumer_key'    => $this->data['consumer_key'],
		    					      'consumer_secret' => $this->data['consumer_secret']);
			$this->load->library('soundcloud',$soundcloud_config);
			
		    //Get the request token
			$token                                  = $this->soundcloud->get_request_token($this->data['callback_url']);
		    $_SESSION['oauth_request_token']        = $token['oauth_token'];
		    $_SESSION['oauth_request_token_secret'] = $token['oauth_token_secret'];
		
		    //build the url for login
		    $this->data['login'] = $this->soundcloud->get_authorize_url($token['oauth_token']);
		}
		/**
		 * end of the dance
		 */
		
		if (!isset($this->data['message']) && $this->session->flashdata('message')) {
			$this->data['message'] = $this->session->flashdata('message');
		}
	}
	
	function index() {	
		// Get basic info about the authicated visitor
	    $this->data['me']      = $this->soundcloud->request('me');
	    $this->data['me']      = new SimpleXMLElement($this->data['me']);
		$this->data['message'] = (isset($this->data['message'])) ? $this->data['message'] : $this->data['me'];
	    $this->data['me']      = get_object_vars($this->data['me']);
	    
	    //display the view
		$this->load->view('soundclouddemo/index.tpl.php',$this->data);
	}
	
	function upload() {
	    if ($this->input->post('submit')) {	//If a track has been submitted
	    	//load the upload library
	    	$this->load->library('upload');
	    	
	    	//setup the upload path
	 		$config['upload_path']    = $this->data['tmp_path'];
	 		
	 		//We have to make sure it's a valid and supported format by SoundCloud.
	        //Note that you also can include artwork for your tracks. Use the same
	        //procedure as for the tracks. PNG, JPG, GIF allowed and a max size of 5MB.
	        //The artwork field is called track[artwork_data].
			$config['allowed_types']  = 'aac|aiff|flac|mp3|ogg|wav';
			$mimes = Array('aac'  => 'video/mp4',
	            		   'aiff' => 'audio/x-aiff',
	           		       'flac' => 'audio/flac',
	           			   'mp3'  => 'audio/mpeg',
	          			   'ogg'  => 'audio/ogg',
	          			   'wav'  => 'audio/x-wav');
	        $extension = explode('.', $_FILES['userfile']['name']);
	        $extension = (isset($extension[count($extension) - 1])) ? $extension[count($extension) - 1] : NULL;
	        $mime      = (isset($mimes[$extension]))                ? $mimes[$extension]                : NULL;
	        
			//if the file already exists overwrite it
			$config['overwrite'] = true;
			
			//set our options for the upload and initialize
			$this->upload->initialize($config);
			
			//upload the file
			if ($this->upload->do_upload()) {
				$post_data = array('track[title]'      => stripslashes($_POST['title']),
                    			   'track[asset_data]' => realpath($config['upload_path'] . $_FILES['userfile']['name']),
                    			   'track[sharing]'    => 'private');

                if ($this->data['response'] = $this->soundcloud->upload_track($post_data, $mime)) {
                    $this->data['response'] = new SimpleXMLElement($this->data['response']);
                    $this->data['response'] = get_object_vars($this->data['response']);
                    	                    
                     // Delete the temporary file.
                    unlink(realpath($config['upload_path'] . $_FILES['userfile']['name']));
                    
		        	$this->data['message'] = 'Success! <a href="' . $this->data['response']['permalink-url'] . '">Your track</a> has been uploaded!';                   
                } 
                else {
		        	$this->data['message'] = 'Something went wrong while talking to SoundCloud, please try again.';
                }
			}
			else {
				$this->data['message'] = $this->upload->display_errors('<br>');
			}
	    }
	    else { //nothing was submitted
	    	$this->data['message'] = 'Please submit a track.';
	    }
	    
	    //save the message and redirect
	    $this->session->set_flashdata('message', $this->data['message']);
	    redirect('soundclouddemo/?oauth_token=' .$_GET['oauth_token'].'&oauth_verifier='.$_GET['oauth_verifier']);
	}
	
	function logout() {
		session_destroy();
		redirect('soundclouddemo');
	}
}

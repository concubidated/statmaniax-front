<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smx extends CI_Controller {

	function __construct() {
        	// Call the Model constructor
        	parent::__construct();

		$this->load->model('data_smx');
        #$this->output->enable_profiler(TRUE);
    }


    public function index()
    {

        $this->data_smx->sync_users();
    
    }
	

    public function sync_scores($date=NULL) {
        $this->data_smx->sync_scores($date);


    }

}

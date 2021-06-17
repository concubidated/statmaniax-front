<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stats extends CI_Controller {

	function __construct() {
        	// Call the Model constructor
        	parent::__construct();
		$this->load->model('api_model');
                $this->load->model('stats_model');
		$this->output->enable_profiler(FALSE);

	}


	public function index() {
		#$this->load->view('updated');
	}

	public function get_song_steps($song_id) {
		$steps = $this->stats_model->get_step_count($song_id, 'wild');
		echo $steps;

	}
	
        public function get_user_steps($userid){
                $data['count'] = $this->api_model->getUserSteps($userid);
		$this->load->view('stats/user_steps', $data);
        }

	public function get_steps($diff=wild) {
		$steps = $this->stats_model->get_all_steps($diff);

	}

    public function update_rankings() {
        $diffs = Array('basic', 'easy', 'hard', 'wild', 'dual', 'full', 'wildfull', 'total');

        foreach ($diffs as $diff) {
            $this->data->ranking_update($diff);
        }
    }

}

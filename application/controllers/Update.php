<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update extends CI_Controller {

	function __construct() {
        	// Call the Model constructor
        	parent::__construct();
		$this->load->model('data_smx');
	}


	public function index() {

		$this->update_all();
		$this->load->view('updated');
	}

        public function update_song_info($song_id) {

                $this->data->update_song_info($song_id);
        }

	public function sync_songs() {
		$this->data_smx->sync_songs();
        }

	public function update_users() {
		$this->data_smx->sync_users();
	}

	public function update_all_scores() {
		$this->update_users();
		
		# One week of scores
		$date = date("Y-m-d", strtotime("-1 week"));
		$this->data_smx->sync_scores($date);
	}

	public function update_songs() {

		$this->data->song_list_generate();
	}

	public function update_leaderboard() {

		$this->data->leaderboard_update();
	}

	public function update_all() {

		$time_start = strtotime("now");

		echo "Current Time: ". date("h:i:sa"). "\n";

		#$this->update_users();
		
		echo "Updating Leaderboards...\n";
		$this->update_leaderboard();
		$time_end = strtotime("now");
		$diff = $time_end-$time_start;
		$minutes = floor($diff/60);
		$seconds = $diff%60;
		echo "Took: $minutes minutes and $seconds seconds\n";
		
		$time_start = strtotime("now");
		echo "Updating Songs...\n";
		$this->sync_songs();
		$time_end = strtotime("now");
		$diff = $time_end-$time_start;
		$minutes = floor($diff/60);
		$seconds = $diff%60;
		echo "Took: $minutes minutes and $seconds seconds\n";		

		$time_start = strtotime("now");
		echo "Updating User Scores...\n";
		$this->update_all_scores();
		$time_end = strtotime("now");
		$diff = $time_end-$time_start;
		$minutes = floor($diff/60);
		$seconds = $diff%60;
		echo "Took: $minutes minutes and $seconds seconds\n";


		#$time_start = strtotime("now");
		#echo "Updating Rankings...\n";
		#$this->update_rankings();
		#$time_end = strtotime("now");
		#$diff = $time_end-$time_start;
		#$minutes = floor($diff/60);
		#$seconds = $diff%60;
		#echo "Took: $minutes minutes and $seconds seconds\n\n";


                #$time_start = strtotime("now");
                #echo "Updating Weekly Rankings...\n";
                #$this->update_weekly_rankings();
                #$time_end = strtotime("now");
                #$diff = $time_end-$time_start;
                #$minutes = floor($diff/60);
                #$seconds = $diff%60;
                #echo "Took: $minutes minutes and $seconds seconds\n\n";

	}

	public function update_rankings() {
		$diffs = Array('basic', 'easy', 'hard', 'wild', 'dual', 'full', 'wildfull', 'total');

		foreach ($diffs as $diff) {
			$this->data->ranking_update($diff);
		}
	}

	public function update_user_ranking($userid, $diff) {
		$this->data->update_ranking_user($userid, $diff);

	}

	public function update_weekly_rankings(){
		#$diffs = Array('basic', 'easy', 'hard', 'wild', 'dual', 'full', 'total');
		$diffs = Array('total');
		foreach ($diffs as $diff) {
			$this->data->ranking_update_weekly($diff);
		}
	}
}

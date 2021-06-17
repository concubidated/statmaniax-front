<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct() {
        	// Call the Model constructor
        	parent::__construct();


        #$this->output->enable_profiler(TRUE);

	}

	public function embed($userid, $diff=Null){
		$data['diff'] = html_escape($diff);
		$data['userid'] = $userid;
		$data['user_info'] = $this->data->user_info_db($userid);

		if(isset($diff)){
			$data['diff'] = html_escape($diff);
			$diff = $this->data->diff_convert($diff);
			$data['user_scores'] = $this->data->user_highscores_title_db($userid, $diff);
			$data['user_stats'] = $this->data->user_stars_unique_db($userid, $diff);
			$data['world_scores'] = $this->data->leaderboard_title_db($diff);
		} else {
			$data['user_stats'] = $this->data->user_stars_unique_db($userid);
			$data['total_records'] = $this->data->user_world_records($userid);
		}
		$this->load->view('embed', $data);
	}


	public function index() {
		$this->load->view('templates/header');
		$this->load->view('home');
		$this->load->view('templates/footer');
	}

	public function news(){
		$data['news'] = $this->data->get_news_db();
		$this->load->view('templates/header');
		$this->load->view('news', $data);
		$this->load->view('templates/footer');
	}

	public function users() {

		// Pull from DB isntead
		$data['users'] = $this->data->user_list_db();
                $this->load->view('templates/header');
		$this->load->view('user_list', $data);
		$this->load->view('templates/footer');

	}


	public function songs($sort=Null){

        	if (isset($_POST['query']) && !empty($_POST['query'])) {
            		$data['songs'] = $this->data->song_list_db($_POST['query']);
            		$data['query'] = $_POST['query'];
        	} elseif  ($sort == 'popular'){
			$data['songs'] = $this->data->most_played_songs();
		} elseif ($sort == 'date'){
			$data['songs'] = $this->data->songs_list_by_date();
                } else {
            		$data['songs'] = $this->data->song_list_db();
        	}

		$this->load->view('templates/header');
		$this->load->view('song_list', $data);
		$this->load->view('templates/footer');
	}

	public function song($songid, $diff='wild'){

		$data['diff'] = $diff;
		$data['songid'] = $songid;
		$diff = $this->data->diff_convert($diff);
		$data['song'] = $this->data->song_info_db($songid);
		$data['scores'] = $this->data->song_highscores_db($data['song'], $diff);
                $data['score_history'] = $this->data->song_score_history_db($data['song'], $diff);

        $this->load->view('templates/header');
		$this->load->view('song', $data);
 		$this->load->view('templates/footer');

	}

	public function scores($userid, $diff=Null){

		if(empty($diff)){
			redirect("/player/$userid/wild");
		}	

		$data['diff'] = $diff;
		$data['userid'] = $userid;
		$diff = $this->data->diff_convert($diff);

		$data['user_stats'] = $this->data->user_stats_db($userid, $diff);
		$data['user_scores']= $this->data->user_highscores_title_db($userid, $diff);
		$data['world_scores'] = $this->data->leaderboard_title_db($diff);
                $data['user_info'] = $this->data->user_info_db($userid);
		
		$this->load->view('templates/header');
		$this->load->view("user_score", $data);
		$this->load->view('templates/footer');

	}

	public function rival($userid, $rivalid, $diff=Null) {

		if(empty($diff))
			redirect("/player/$userid/compare/$rivalid/wild");

		$data['diff'] = $diff;
		$data['userid'] = $userid;
		$data['rivalid'] = $rivalid;
		$diff = $this->data->diff_convert($diff);

        $view = "user_score";

		$data['user_stats'] = $this->data->user_stats_db($userid, $diff);
		$data['user_scores']= $this->data->user_highscores_title_db($userid, $diff);
		$data['user_info'] = $this->data->user_info_db($userid);


        if (isset($rivalid)) {
            if ($rivalid == "world") {
                $data['world_scores'] = $this->data->leaderboard_title_db($diff);
                $view = "user_world";
            } else {
                $data['rival_scores'] = $this->data->user_highscores_title_db($rivalid, $diff);
                $data['rival_info'] = $this->data->user_info_db($rivalid);
                $view = "rival_user";
            }
        }

		$this->load->view('templates/header');
        $this->load->view($view, $data);
		$this->load->view('templates/footer');

	}

    public function search()
    {

        $this->db->group_by('country');
        $data['countries'] = $this->db->get('user')->result_array();

        if (isset($_POST['search'])) {
            if (isset($_POST['country'])) {
                $this->db->where('country', $_POST['country']);
                $data['results'] = $this->db->get('user')->result_array();
            } else {
                $this->db->like('username', $_POST['query']);
                $data['results'] = $this->db->get('user')->result_array();
            }

            $this->load->view('templates/header');
            $this->load->view("search/results", $data);
            $this->load->view('templates/footer');
        } else {
            $this->load->view('templates/header');
            $this->load->view("search/prompt", $data);
            $this->load->view('templates/footer');
        }
    }

    function userlist()
    {
        if (isset($_POST['search'])) {
            $this->db->like('username', $_POST['search']);
        }
		$this->db->select('id, username, total_score, country, picture_path');
        die(json_encode($this->db->get('user')->result_array()));
    }


}

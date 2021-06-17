<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Data_smx extends CI_Model {
    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();

        // get db objects for smx database
	$this->smx = $this->load->database('smx', TRUE);


    }


    function sync_songs(){
        $this->smx->join('song_charts as sc', 'on sc.song_id = s.id');
        $this->smx->join('difficulties as d', 'on d.id = sc.difficulty_id');
        $this->smx->select('s.id, s.game_song_id as song_id, s.title, s.subtitle, s.artist, s.cover_path, s.genre, s.label, s.bpm, s.website, s.created_at, s.updated_at, sc.difficulty, d.name'); 
        $results = $this->smx->get('songs as s')->result_array();


	$songs = array();
	foreach($results as $song){
		if(!isset($songs[$song['id']]))
			$songs[$song['id']] = $song;
		$songs[$song['id']][$song['name']] = $song['difficulty'];

	}

	foreach($songs as $song){
	
		if(!isset($song['team']))
			$song['team'] = 0;

		$data = array(
			'id' => $song['id'],
			'game_song_id' => $song['song_id'],
			'title' => $song['title'],
			'subtitle' => $song['subtitle'],
			'artist' => $song['artist'],
			'genre' => $song['genre'],
			'label' => $song['label'],
			'bpm' => $song['bpm'],
			'cover_path' => $song['cover_path'],
			'website' => $song['website'],
			'basic' => $song['basic'],
			'easy' => $song['easy'],
			'hard' => $song['hard'],
			'wild' => $song['wild'],
			'dual' => $song['dual'],
			'full' => $song['full'],
			'team' => $song['team'],
			'updated_at' => $song['updated_at'],
			'created_at' => $song['created_at']
		);


        	$sql = $this->db->replace('song', $data);
	}
    }

    function sync_users(){
        $query = $this->smx->get('gamers');
        $users = $query->result_array();

	foreach($users as $user){

            $this->smx->select('sum(score) as total_score');
            $this->smx->where('gamer_id', $user['id']);
            $this->smx->where('is_enabled', 1);
            $result = $this->smx->get('gamer_scores')->row_array();

            if(!$user['picture_path'])
                $user['picture_path'] = '';
            $data = array(
                'id' => $user['id'],
                'first' => $this->db->escape($user['first_name']),
                'last' => $this->db->escape($user['last_name']),
                'username' => $user['username'],
                'picture_path' => $user['picture_path'],
                'country' => $user['country'],
            );

            $sql = $this->db->insert_string('user', $data) . " ON DUPLICATE KEY UPDATE first=$data[first], last=$data[last], total_score='$result[total_score]', picture_path='$data[picture_path]', country='$data[country]'";
            $this->db->query($sql);
	} 
    }



    function sync_scores($date=NULL){

        if($date){
            $this->smx->where("gs.created_at >", "$date");
        }

        $this->smx->join('song_charts as sc', 'on sc.id = gs.song_chart_id');
        $this->smx->join('difficulties as d', 'on d.id = sc.difficulty_id');
        $this->smx->join('songs as s', 'on s.id = sc.song_id');
        $this->smx->from('gamer_scores as gs');
        $this->smx->where('gs.is_enabled', 1);
	$this->smx->where('gs.score <=', '100000');
        $query = $this->smx->select('s.id as song_id, gamer_id, gs.song_chart_id, gs.score, machine_serial, grade, calories, perfect1, perfect2, early, late, misses, flags, green, yellow, red, gs.created_at, s.title, s.artist, s.cover_path, d.name, gs.uuid')->get();

	$scores = $query->result_array();
        foreach($scores as $score){

            $data = array(
                'song_id' => $score['song_id'],
                'gamer_id' => $score['gamer_id'],
                'song_chart_id' => $score['song_chart_id'],
                'score' => $score['score'],
                'machine_serial' => $score['machine_serial'],
                'grade' => $score['grade'],
                'calories' => $score['calories'],
                'perfect1' => $score['perfect1'],
                'perfect2' => $score['perfect2'],
                'early' => $score['early'],
                'late' => $score['late'],
                'misses' => $score['misses'],
                'flags' => $score['flags'],
                'green' => $score['green'],
                'yellow' => $score['yellow'],
                'red' => $score['red'],
                'created_at' => $score['created_at'],
                'title' => $score['title'],
                'artist' => $score['artist'],
                'cover_path' => $score['cover_path'],
                'name' => $score['name'],
                'uuid' => $score['uuid']
            );

            $insert = $this->db->insert_string('score', $data);
            $insert = str_replace('INSERT INTO','INSERT IGNORE INTO', $insert);
            $this->db->query($insert);
        } 

        echo sizeof($scores)." scores updated\n";

    }


}

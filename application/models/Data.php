<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Data extends CI_Model {
    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();

    }


    function get_news_db(){
	$sql = "SELECT * from news order by date DESC";
	return $this->db->query($sql)->result_array();

    }

    function user_update($user_list){
		foreach ($user_list as $user){

			if(strpos($user['picture_path'], "avatar")){

				$id = $this->db->escape($this->parse_picture_path($user['picture_path']));
				$first = $this->db->escape($user['first_name']);
				$last = $this->db->escape($user['last_name']);
				$username = $this->db->escape($user['username']);
				$score = $this->db->escape($user['total_score']);
				$picture_path = $this->db->escape($user['picture_path']);
				$country = $this->db->escape($user['country']);

				$sql = "INSERT into user (id, username, first, last, total_score, picture_path, country)
					VALUES ($id,$username,$first,$last,$score,$picture_path,$country)
					ON DUPLICATE KEY UPDATE first=$first, last=$last, total_score=$score, picture_path=$picture_path, country=$country";
				$this->db->query($sql);
			}
		}
    }


	function user_profile_update(){
		$user_list = $this->db->select('id')->get('user')->result_array();

		foreach($user_list as $user){
			$id = $user['id'];
			$url = "https://data.stepmaniax.com/gamer/profile/$id";


            $options = array(
                'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\nauth-gamer: 3\r\n",
                'method'  => 'POST',
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
			if ($result === FALSE) { return; }

            $user_profile = json_decode(json_decode($result)->gamer, true);

			if(!$user_profile['picture_path'])
				$user_profile['picture_path']="";

            $id = $this->db->escape($id);
            $first = $this->db->escape($user_profile['first_name']);
            $last = $this->db->escape($user_profile['last_name']);
            $username = $this->db->escape($user_profile['username']);
            $score = $this->db->escape($user_profile['score']);
            $picture_path = $this->db->escape($user_profile['picture_path']);
            $country = $this->db->escape($user_profile['country']);

            $sql = "INSERT into user (id, username, first, last, total_score, picture_path, country)
                    VALUES ($id,$username,$first,$last,$score,$picture_path,$country)
                    ON DUPLICATE KEY UPDATE first=$first, last=$last, total_score=$score, picture_path=$picture_path, country=$country";
            $this->db->query($sql);

			#echo $id.":\t".$username."\t".$score."\n";

         }
	}

	function get_gamer_more($limit=0){


		$url = "https://data.stepmaniax.com/gamer/more/public";

		$users = array();
		while(true){
			$postdata = http_build_query(
				array('shown_gamers' => $limit)
			);


			$options = array(
				'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\nauth-gamer: 3\r\n",
				'method'  => 'POST',
				'content' => $postdata
				)
			);

			$context = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			if ($result === FALSE) { return; }


			$total_users = json_decode($result)->total_gamers;
			if (($total_users-$limit) < 0)
				break;

			$gamer_list = json_decode(json_decode($result)->public_gamers, true);
			foreach($gamer_list as $user){
				$users[$user['id']] = $user;

				$data = array(
						'id' => $user['id'],
						'username' => $user['username'],
						'first' => $user['first_name'],
						'last' => $user['last_name'],
						'country' => $user['country'],
						'picture_path' => $user['picture_path']
				);

				#echo $user['id'].": ".$user['username']."\n";

				#add user to database
				$insert_query = $this->db->insert_string('user', $data);
				$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
				$this->db->query($insert_query);
			}

			$limit+=10;
		}
	}


	function update_song_info($song_id){

		$url = "https://data.stepmaniax.com/song/".$song_id."/view";
		$options = array(
			'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\nauth-gamer: 3\r\n",
			'method'  => 'POST',
			)
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { return; }

		$song = json_decode(json_decode($result)->song, true);

		$diff = Array();
		foreach($song['charts'] as $chart){
			$diff[$chart['difficulty_name']] = $chart['difficulty'];
		}

		if(array_key_exists('basic', $diff)) $basic = $diff['basic']; else $basic=0;
		if(array_key_exists('easy', $diff)) $easy = $diff['easy']; else $easy=0;
		if(array_key_exists('hard', $diff)) $hard = $diff['hard']; else $hard=0;
		if(array_key_exists('wild', $diff)) $wild = $diff['wild']; else $wild=0;
		if(array_key_exists('dual', $diff)) $dual = $diff['dual']; else $dual=0;
		if(array_key_exists('full', $diff)) $full = $diff['full']; else $full=0;
		if(array_key_exists('team', $diff)) $team = $diff['team']; else $team=0;

		$data = array(
			'title' => $song['title'],
			'subtitle' => $song['subtitle'],
			'artist' => $song['artist'],
			'genre' => $song['genre'],
			'label' => $song['label'],
			'bpm' => $song['bpm'],
			'cover_path' => substr($song['cover_path'], 0, -10),
			'website' => $song['website'],
			'basic' => $basic,
			'easy' => $easy,
			'hard' => $hard,
			'wild' => $wild,
			'dual' => $dual,
			'full' => $full,
			'team' => $team
		);

		$this->db->where('id', $song_id);
		$this->db->update('song', $data);

	}

    function song_list_generate(){

        $data = file_get_contents('https://data.stepmaniax.com/index.php/web/leaderboard/song?difficulty_id=1');
        $json = json_decode($data, true);
        $num_pages = $json['results']['last_page'];
        $current_page = $json['results']['current_page'];

        $song_list = $json['results']['data'];
        while($current_page < $num_pages){
		$next_page = $current_page+1;
                $url = "https://data.stepmaniax.com/index.php/web/leaderboard/song?difficulty_id=1&page=".$next_page;
                $data = file_get_contents($url);
                $json = json_decode($data, true);
                $song_list = array_merge($song_list, $json['results']['data']);
                $current_page = $json['results']['current_page'];
        }


	// now add/update to database
	foreach($song_list as $song){


		$id = $this->db->escape($song['id']);
                $game_song_id = $this->db->escape($song['game_song_id']);
                $title = $this->db->escape($song['title']);
                $subtitle = $this->db->escape($song['subtitle']);
                $artist = $this->db->escape($song['artist']);
                $genre = $this->db->escape($song['genre']);
                $label = $this->db->escape($song['label']);
                $website = $this->db->escape($song['website']);
                $bpm = $this->db->escape($song['bpm']);
                $cover_path = $this->db->escape($song['cover_path']);
                $updated_at = $this->db->escape($song['updated_at']);

		$sql = "INSERT into song (id, game_song_id, title, subtitle, artist, genre, label, website, bpm, cover_path, updated_at)
			VALUES ($id, $game_song_id, $title, $subtitle, $artist, $genre, $label, $website, $bpm, $cover_path, $updated_at)
			ON DUPLICATE KEY UPDATE subtitle=$subtitle, genre=$genre, label=$label, website=$website, bpm=$bpm, cover_path=$cover_path, updated_at=$updated_at";

		$this->db->query($sql);
	}

   }



    function user_list_db(){

	$this->db->order_by('total_score', 'desc');
	$query = $this->db->get('user');
	return $query->result_array();

    }

    function songs_list_by_date(){
	$this->db->order_by('created_at', 'DESC');
	$query = $this->db->get('song');
	return $query->result_array();

    }

    function song_list_db($query = null)
    {
        if ($query != null) {
            $this->db->like('title', $query);

        }
        $query = $this->db->get('song');
        return $query->result_array();
    }


    function user_info_db($userid){
		if(is_numeric($userid)){
			$this->db->escape($userid);
			$sql = "SELECT * from user where id = $userid";
			$query = $this->db->query($sql);
			$results = $query->row_array();
			if (!$results['picture_path'])
				$results['picture_path'] = "uploads/b72a65b1910f794996364e8fdd25216bf84e2bb7.jpg";
			return $results;
		}

    }

    function user_stats_db($userid, $diff=Null){
	if(isset($diff)) {
		if(is_numeric($diff))
			$diff = $this->diff_convert($diff);
		$userid = $this->db->escape($userid);
                $diff = $this->db->escape($diff);
		$sql = "SELECT grade, count(*) as count FROM `score` WHERE gamer_id=$userid and name = $diff group by grade";
	} else {
		$sql = "SELECT grade, count(*) as count FROM score WHERE gamer_id=$userid group by grade";
	}

	$query = $this->db->query($sql);
	return $query->result_array();

    }

    function user_stars_unique_db($userid, $diff=Null){
	$userid = $this->db->escape($userid);
	if(isset($diff)) {
		if(is_numeric($diff))
			$diff = $this->diff_convert($diff);
		$diff = $this->db->escape($diff);
		$sql =  "select grade, count(*) as count from (select title, artist, grade, name, count(*) as count FROM `score`
                        WHERE gamer_id=$userid and name=$diff  group by grade, title, artist, name) results
                        group by grade";
	} 	else {
		$sql = "select grade, count(*) as count from (select title, artist, grade, name, count(*) as count FROM `score`
			WHERE gamer_id=$userid group by grade, title, artist, name) results
			group by grade";
	}
	$query = $this->db->query($sql);
	return $query->result_array();

    }

    function user_world_records($userid){
	$userid = $this->db->escape($userid);

	$sql = "select title, artist, name, score from score where gamer_id=$userid";
	$query = $this->db->query($sql);
	$highscores = $query->result_array();

	$sql = "select * from leaderboard";
	$query = $this->db->query($sql);
        $world = $query->result_array();

	$leaderboard = Array();
	foreach ($world as $score)
		$leaderboard[$score['id'].$score['diff']] = $score;

	$user_scores = Array();
        foreach($highscores as $score){
		$title = $score['title'];
		$artist = $score['artist'];
		$diff = $this->diff_convert($score['name']);
		if (isset($user_scores[$title.$artist.$diff])){
			if($user_scores[$title.$artist.$diff]['score'] < $score['score'])
				$user_scores[$title.$artist.$diff] = $score;
		} else {
			$user_scores[$title.$artist.$diff] = $score;
		}
	}

	$wr=0;
	foreach($user_scores as $score){
		$diff = $this->diff_convert($score['name']);
		if($leaderboard[$score['title'].$score['artist'].$diff]['score'] <= $score['score'])
			$wr+=1;

	}
	return $wr;

    }

    function song_info_db($songid){
	$query = $this->db->get_where('song', array('game_song_id' => $songid));
	return $query->row_array();

    }

    function song_scores_db($title, $artist){
	$title = $this->db->escape($title);
        $artist = $this->db->escape($artist);
	$sql = "select * from score
		inner join user
		on user.id = score.id
		where score.title=$title and score.artist=$artist
		order by score desc";

	$query = $this->db->query($sql);
	return $query->result_array();


    }

    function user_list_api() {

        $data = file_get_contents('https://data.stepmaniax.com/index.php/web/leaderboard/user');
        $json = json_decode($data, true);


	$num_pages = $json['results']['last_page'];
	$current_page = $json['results']['current_page'];

        $user_list = $json['results']['data'];
	while($current_page < $num_pages){
		$data = file_get_contents($json['results']['next_page_url']);
		$json = json_decode($data, true);
		$user_list = array_merge($user_list, $json['results']['data']);
        	$current_page = $json['results']['current_page'];
	}	

	return $user_list;

    }


    function user_info_api($userid) {

        $data = file_get_contents('https://data.stepmaniax.com/index.php/web/leaderboard/user');
        $json = json_decode($data, true);


        $num_pages = $json['results']['last_page'];
        $current_page = $json['results']['current_page'];

        $user_list = $json['results']['data'];
        while($current_page < $num_pages){
		foreach ($user_list as $user){
			if (strpos($user['picture_path'], "avatar")){
				$id = $this->parse_picture_path($user['picture_path']);
				if ($userid == $id){
					return $user;
				}
			}
		}
                $data = file_get_contents($json['results']['next_page_url']);
                $json = json_decode($data, true);
                $user_list = array_merge($user_list, $json['results']['data']);
                $current_page = $json['results']['current_page'];
        }
	return Array();

    }


    function user_score_history_api($userid){
        $data = file_get_contents('https://data.stepmaniax.com/index.php/web/gamer/history/'.$userid);
        $json = json_decode($data, true);

        $num_pages = $json['scores']['last_page'];
        $current_page = $json['scores']['current_page'];

        $score_list = $json['scores']['data'];
        while($current_page < $num_pages){
                $data = file_get_contents($json['scores']['next_page_url']);
                $json = json_decode($data, true);
                $score_list = array_merge($score_list, $json['scores']['data']);
                $current_page = $json['scores']['current_page'];
        }

		#if($userid == 3){
		#	echo "<pre>";
        #    print_r($score_list);
		#	echo "</pre>";
        #}

        return $score_list;



    }


    function user_score_history_update($data){

	foreach($data as $score){

		if($score['score'] <= 100000){

		$id = $this->db->escape($score['id']);
                $song_id = $this->db->escape($this->get_song_id_by_text($score['title'], $score['artist']));
                $gamer_id = $this->db->escape($score['gamer_id']);
                $song_chart_id = $this->db->escape($score['song_chart_id']);
                $score_points = $this->db->escape($score['score']);
                $machine_serial = $this->db->escape($score['machine_serial']);
                $grade = $this->db->escape($score['grade']);
                $calories = $this->db->escape($score['calories']);
                $perfect1 = $this->db->escape($score['perfect1']);
                $perfect2 = $this->db->escape($score['perfect2']);
                $early = $this->db->escape($score['early']);
                $late = $this->db->escape($score['late']);
                $misses = $this->db->escape($score['misses']);
                $flags = $this->db->escape($score['flags']);
                $green = $this->db->escape($score['green']);
                $yellow = $this->db->escape($score['yellow']);
                $red = $this->db->escape($score['red']);
                $created_at = $this->db->escape($score['created_at']);
                $title = $this->db->escape($score['title']);
                $artist = $this->db->escape($score['artist']);
                $cover_path = $this->db->escape($score['cover_path']);
                $name = $this->db->escape($score['name']);
                $uuid = $this->db->escape($score['uuid']);

		$sql = "INSERT into score (id,song_id,gamer_id,song_chart_id,score,machine_serial,grade,calories,perfect1,perfect2,early,late,misses,flags,green,yellow,red,created_at,title,artist,cover_path,name,uuid)
			VALUES($id,$song_id,$gamer_id,$song_chart_id,$score_points,$machine_serial,$grade,$calories,$perfect1,$perfect2,$early,$late,$misses,$flags,$green,$yellow,$red,$created_at,$title,$artist,$cover_path,$name,$uuid)
			ON DUPLICATE KEY UPDATE song_id=$song_id,artist=$artist,title=$title,created_at=$created_at";
		$this->db->query($sql);
		}

	}




    }

    function user_highscores($userid){
	$data = file_get_contents('https://data.stepmaniax.com/index.php/web/gamer/history/'.$userid);
        $json = json_decode($data, true);

        $num_pages = $json['scores']['last_page'];
        $current_page = $json['scores']['current_page'];

        $score_list = $json['scores']['data'];
	$highscores = Array();
        while($current_page <= $num_pages){
		foreach($score_list as $score) {

			$score['song_id'] = $this->get_song_id_by_text($score['title'], $score['artist']);

			print_r($score);
			exit();
			$song_chart_id = $score['song_chart_id'];
			if(isset($highscores[$song_chart_id])){
				if($highscores[$song_chart_id]['score'] < $score['score'])
		                        $highscores[$song_chart_id] = $score;
			} else {
				$highscores[$song_chart_id] = $score;
			}
		}

                $data = file_get_contents($json['scores']['next_page_url']);
                $json = json_decode($data, true);
                $score_list = array_merge($score_list, $json['scores']['data']);
                $current_page = $json['scores']['current_page'];
        }

        return $highscores;

    }

  function user_highscores_title($userid, $diff=4){
        $data = file_get_contents('https://data.stepmaniax.com/index.php/web/gamer/history/'.$userid);
        $json = json_decode($data, true);

        $num_pages = $json['scores']['last_page'];
        $current_page = $json['scores']['current_page'];

	$diff = $this->diff_convert($diff);


        $score_list = $json['scores']['data'];
        $highscores = Array();
        while($current_page <= $num_pages){
                foreach($score_list as $score) {
                        $song_title = $score['title'];
			$song_artist = $score['artist'];
			$song_diff = $score['name'];
			if ($song_diff == $diff){
				if(isset($highscores[$song_title.$song_artist])){
					if($highscores[$song_title.$song_artist]['score'] < $score['score'])
						$highscores[$song_title.$song_artist] = $score;
				} else {
					$highscores[$song_title.$song_artist] = $score;
				}
			}
                }

		if($current_page == $num_pages)
			break;

                $data = file_get_contents($json['scores']['next_page_url']);
                $json = json_decode($data, true);
                $score_list = array_merge($score_list, $json['scores']['data']);
                $current_page = $json['scores']['current_page'];
        }

        return $highscores;

    }


    function user_highscores_title_db($userid, $diff=4){

        $diff = $this->diff_convert($diff);

	$userid = $this->db->escape($userid);
        $diff = $this->db->escape($diff);

        $sql = "select *, score.created_at as date from score
		inner join song on score.title=song.title and score.artist=song.artist
		where gamer_id=$userid and name=$diff";
	$query = $this->db->query($sql);
	$scores = $query->result_array();
        $highscores = Array();
	foreach ($scores as $score){
		$song_title = $score['title'];
 		$song_artist = $score['artist'];
 		if(isset($highscores[$song_title.$song_artist])){
			if($highscores[$song_title.$song_artist]['score'] < $score['score'])
				$highscores[$song_title.$song_artist] = $score;
		} else {
			$highscores[$song_title.$song_artist] = $score;
		}
	}
	return $highscores;
    }

    function get_song_id_by_text($title, $artist)
    {
        $this->db->select('id');
        $this->db->where('title', $title);
	    $this->db->where('artist', $artist);
        $out = $this->db->get('song')->result_array()[0];
        return $out['id'];
    }

    function song_score_history_db($song, $diff = 4) {
        $diff = $this->diff_convert($diff);

        $this->db->where('title', $song['title']);
        $this->db->where('artist', $song['artist']);
        $this->db->where('name', $diff);
        $this->db->join('user', 'user.id = score.gamer_id');
	$this->db->order_by('created_at', 'DESC');
        return $this->db->get('score')->result_array();
    }


    function song_highscores_db($song, $diff){
        $diff = $this->diff_convert($diff);

	$artist = $this->db->escape($song['artist']);
	$title = $this->db->escape($song['title']);
	$diff =  $this->db->escape($diff);

	$sql = "SELECT * FROM score
		INNER JOIN
		(SELECT gamer_id, max(score) AS score FROM score WHERE title=$title AND artist=$artist AND name=$diff GROUP BY gamer_id) maxscore
		ON (score.gamer_id = maxscore.gamer_id and score.score = maxscore.score)
		INNER JOIN user
		ON score.gamer_id = user.id
		WHERE title=$title AND artist=$artist AND name=$diff
		ORDER BY score.score DESC";
	#$sql = "SELECT user.username, user.picture_path, `grade`, `perfect1`, `perfect2`, `early`, `late`, `misses`, `flags`, `green`, `yellow`, `red`, gamer_id, max(score) AS score, created_at FROM score 
	#	INNER JOIN user ON
	#	user.id = score.gamer_id
	#	WHERE title=$title AND artist=$artist AND name=$diff GROUP BY gamer_id  
	#	ORDER BY `score`  DESC";


	$query = $this->db->query($sql);
	$scores =  $query->result_array();
	$highscores = Array();
	foreach($scores as $score){
		if(isset($highscores[$score['gamer_id']])){
			if($highscores[$score['gamer_id']]['created_at'] < $score['created_at'])
				$highscore[$score['gamer_id']] = $score;
		} else
			$highscores[$score['gamer_id']] = $score;

	}
	return $highscores;

    }


    function leaderboard_title_api($diff){

        $data = file_get_contents('https://data.stepmaniax.com/index.php/web/leaderboard/song?&search=&difficulty_id='.$diff);
        $json = json_decode($data, true);


        $num_pages = $json['results']['last_page'];
        $current_page = $json['results']['current_page'];


        $score_list = $json['results']['data'];
        $highscores = Array();
        while($current_page <= $num_pages){
                foreach($score_list as $score){
                        $song_title = $score['title'];
                        $song_artist = $score['artist'];
			if (!empty($score['top_scores']))
                        	$highscores[$song_title.$song_artist] = $score['top_scores'][0];

                }
                $next_page = $current_page+1;
                $url = "https://data.stepmaniax.com/index.php/web/leaderboard/song?&search=&difficulty_id=".$diff."&page=".$next_page;
                $data = file_get_contents($url);
                $json = json_decode($data, true);
        	$score_list = $json['results']['data'];
                $current_page = $json['results']['current_page'];
        }
        return $highscores;

    }

    function leaderboard_title_db($diff){
	$diff = $this->db->escape($diff);
	$sql = "select l.id as id, l.score as score, u.id as user_id, u.username as username 
		from leaderboard as l
		left join user u 
		on u.id=l.user_id
		where diff=$diff";
	#$sql = "select * from leaderboard where diff=$diff";
	$query = $this->db->query($sql);

	$leaderboard = $query->result_array();
        $highscores = Array();
	foreach($leaderboard as $score){
		$key = $score['id'];
		$highscores[$key] = $score;
	}

	return $highscores;

    }

    function leaderboard_update(){

	for ( $diff=6; $diff>0; $diff--){

		$highscores = $this->leaderboard_title_api($diff);

		foreach($highscores as $key=>$score){
            if(strpos($score['picture_path'],'avatar'))
			    $userid = $this->db->escape($this->parse_picture_path($score['picture_path']));
		    else
			$userid = NULL;

			$userid = $this->db->escape($userid);
			$diff = $this->db->escape($diff);
			$key = $this->db->escape($key);
			$score_points = $this->db->escape($score['score']);

			$sql = "INSERT into leaderboard (id, user_id, score, diff) VALUES ($key, $userid, $score_points, $diff)
				ON DUPLICATE KEY UPDATE id=$key, user_id=$userid, score=$score_points, diff=$diff";
			$this->db->query($sql);
		}
	}// end for diff
    }



	function update_ranking_user($userid, $diff, $date='all'){
		$diff_str = $this->db->escape($diff);

		$diff_list = array("basic", "easy", "hard", "wild", "dual", "full");


		$where = "";
		if ($date != "all"){
			$tzoffset = date('Z')/60/60;
			$utc_date = new DateTime($date);
			#date_modify($utc_date, $tzoffset.' hours');
			$utc_date = date_format($utc_date, 'Y-m-d H:i:s');
			$where = "and score.created_at >= '$utc_date'";
		}

		$results_total = array();

		if(strpos($diff, "total") !== false) {
		    if($date != 'all'){
			foreach($diff_list as $difficulty){
				$sql = "SELECT gamer_id, basic, easy, hard, wild, `dual`, full, max(score) AS score from score
				inner join song
				on song.title = score.title and song.artist = score.artist
				where name='$difficulty' and gamer_id=$userid $where GROUP BY song_chart_id";
				$query = $this->db->query($sql);
				$results_total[$difficulty] = $query->result_array();
			}
		    } else {
			$sql = "SELECT rank, name from ranking where name != 'total' and name != 'wildfull'  and user_id = $userid";
			$result = $this->db->query($sql)->result_array();
			$total_ranking = 0;
			foreach($result as $ranking) {
				$total_ranking = $total_ranking+$ranking['rank'];
			}

				#echo $userid.": ".$total_ranking."\n";
				$sql = "INSERT into ranking (`user_id`, `rank`, `name`) VALUES
                        ($userid, $total_ranking, '$diff') ON DUPLICATE KEY UPDATE `rank`=$total_ranking, `updated_at`=NOW()";
				$this->db->query($sql);
				return;
		    }

		} elseif (strpos($diff, "wildfull") !== false) {
			$sql = "SELECT rank, name from ranking where (name = 'wild' or name = 'full') and (user_id = $userid)";
                        $result = $this->db->query($sql)->result_array();
                        $total_ranking = 0;
                        foreach($result as $ranking) {
                                $total_ranking = $total_ranking+$ranking['rank'];
                        }
                                $sql = "INSERT into ranking (`user_id`, `rank`, `name`) VALUES
                        ($userid, $total_ranking, '$diff') ON DUPLICATE KEY UPDATE `rank`=$total_ranking, `updated_at`=NOW()";
                                $this->db->query($sql);
                                return;



		} else {
			$sql = "SELECT gamer_id, basic, easy, hard, wild, `dual`, full, max(score) AS score from score
			inner join song
			on song.title = score.title and song.artist = score.artist
			where name=$diff_str and gamer_id=$userid $where GROUP BY song_chart_id";
			$query = $this->db->query($sql);
			$scores = $query->result_array();
		}

		$rank = 0;
		$weight = 2;
		// Ranking Algorigm is simple right now, just take the level of the chart and multiple by the difficulty
		// this makes it so the harder songs are weighted more. maybe this is too aggresive though. Only time will tell!

		if (strpos($diff, "total") !== false) {
			foreach($diff_list as $difficulty){
				foreach ($results_total[$difficulty] as $score)
					$rank += (pow($score[$difficulty], $weight)) * $score['score'];
			}
		} else {

			foreach ($scores as $score){
				$diff = strtolower($diff);
				$rank += (pow($score[$diff], $weight)) * $score['score'];
			}

		}

		if ($rank == 0)
			return;

		$rank = $this->db->escape($rank/1000);

		#echo "$userid: $rank \n";
		if($date != 'all'){

			// I am not proud of this
			#date_default_timezone_set('UTC');
			$day = gmdate('N', strtotime('now'));
			#date_default_timezone_set('America/Los_Angeles');
			if ($day < 2){
				$day = $day+5;
			} else {
				$day = $day-2;
			}
			$sql = "INSERT into weekly_ranking (`user_id`, `rank`, `name`, `start_date`, `$day`) VALUES
            		($userid, $rank, $diff_str, '$date', $rank) ON DUPLICATE KEY UPDATE `rank`=$rank, `updated_at`=NOW(), `$day`=$rank";
			#echo $sql."<br>";
		} else {
			$sql = "INSERT into ranking (`user_id`, `rank`, `name`) VALUES
			($userid, $rank, $diff_str) ON DUPLICATE KEY UPDATE `rank`=$rank, `updated_at`=NOW()";
		}
		$this->db->query($sql);

	}

	function ranking_update($diff) {
		$users = $this->user_list_db();
		foreach ($users as $user){
			if ($user['total_score'] > 0){
				$this->update_ranking_user($user['id'], $diff);
			}
		}
	}


#	function is_new_week($start_day) {
#		$this->db->order_by('start_day', 'DESC');
#		$this->db->limit(1);
#		$this->db->select('start_day');
#		$result = $this->db->get('weekly_ranking')->row();
#		if($result == $start_day){
#			return False;
#		} else {
#			return True;
#		}
#	}

	function ranking_update_weekly($diff) {
		$rollover_day = "Tuesday";
		date_default_timezone_set('UTC');

		$now = strtotime('now');
		#$now = mktime(23, 0, 0, 6, 14, 2021);

		if (date("l", $now) == $rollover_day){
			$start_day = date("Y-m-d", $now);
		} else  {
			$start_day = date( "Y-m-d", strtotime("last $rollover_day", $now));
		}

		#echo $start_day."\n";
		$users = $this->user_played_this_week($start_day);
		if ( $users ) {
			foreach ($users as $user){
				#echo "$user[gamer_id], $start_day<br>";
				$this->update_ranking_user($user['gamer_id'], $diff, $start_day);
			}
		}
	}

	function user_played_this_week($start_day){
		$date = new DateTime($start_day);
		$start_day = date_format($date, 'Y-m-d H:i:s');
		$this->db->where('created_at >=',$start_day);
		$this->db->group_by('gamer_id');
		$this->db->select('gamer_id');
		return $this->db->get('score')->result_array();
	}

    function parse_picture_path($picture_path){
        $parts = explode('/', $picture_path);
        $image = $parts[2];
        $parts = explode('_', $image);
        return $parts[0];

    }

    function diff_convert($diff){

	if(is_numeric($diff)){
	    switch($diff){
                case "1":
                        $diff = "basic";
                        break;
                case "2":
                        $diff = "easy";
                        break;
                case "3":
                        $diff = "hard";
                        break;
                case "4":
                        $diff = "wild";
                        break;
                case "5":
                        $diff = "dual";
                        break;
                case "6":
                        $diff = "full";
                        break;
                case "default":
                        break;
            }
	} else {
	    switch($diff){
                case "basic":
                        $diff = "1";
                        break;
                case "easy":
                        $diff = "2";
                        break;
                case "hard":
                        $diff = "3";
                        break;
                case "wild":
                        $diff = "4";
                        break;
                case "dual":
                        $diff = "5";
                        break;
                case "full":
                        $diff = "6";
                        break;
                case "default":
                        break;

            }

	}

	return $diff;

     }

    function gradetostars($stars)
    {
        $out = "";
        if (is_numeric($stars)) {
            switch ($stars) {
                case 6:
                    $out = "https://www.stepmaniax.com/img/grades/1.png";
                    break;
                case 5:
                    $out = "https://www.stepmaniax.com/img/grades/2.png";
                    break;
                case 4:
                    $out = "https://www.stepmaniax.com/img/grades/3.png";
                    break;
                case 3:
                    $out = "https://www.stepmaniax.com/img/grades/4.png";
                    break;
                case 2:
                    $out = "https://www.stepmaniax.com/img/grades/5.png";
                    break;
                case 1:
                    $out = "https://www.stepmaniax.com/img/grades/6.png";
                    break;
                case 0:
                    $out = "https://www.stepmaniax.com/img/grades/7.png";
                    break;
            }
        } else {
            switch ($stars) {
                case "6":
                    $out = "https://www.stepmaniax.com/img/grades/1.png";
                    break;
                case "5":
                    $out = "https://www.stepmaniax.com/img/grades/2.png";
                    break;
                case "4":
                    $out = "https://www.stepmaniax.com/img/grades/3.png";
                    break;
                case "3":
                    $out = "https://www.stepmaniax.com/img/grades/4.png";
                    break;
                case "2":
                    $out = "https://www.stepmaniax.com/img/grades/5.png";
                    break;
                case "1":
                    $out = "https://www.stepmaniax.com/img/grades/6.png";
                    break;
                case "0":
                    $out = "https://www.stepmaniax.com/img/grades/7.png";
                    break;
            }
        }

        return $out;
    }

    function getRank($user, $type)
    {
        $this->db->select('rank');
        $this->db->where('user_id', $user);
        $this->db->where('name', $type);
        $this->db->where('rank >', 0);
        return $this->db->get('ranking')->result_array()[0]['rank'];

    }

    function get_ranking_data($diff)
    {
        $this->db->where('name', $diff);
        $this->db->where('rank >', 0);
        $this->db->order_by('rank', 'desc');
        $this->db->join('user', 'user.id = ranking.user_id');
        return $this->db->get('ranking')->result_array();
    }

    function most_played_songs(){
	$sql = "SELECT count(*) as count, score.title, score.artist, score.cover_path, song.website, song.game_song_id, song.genre, song.label, song.bpm FROM `score` 
		inner join song on 
		song.artist = score.artist 
		and
		song.title = score.title
		group by `title`,`artist`
		order by count desc";
	$query = $this->db->query($sql);
	return $query->result_array();
    }

}

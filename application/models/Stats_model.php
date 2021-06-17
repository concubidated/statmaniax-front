<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Stats_model extends CI_Model {

    private $tz_offset;

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();

        // get db objects for smx database
	#$this->smx = $this->load->database('smx', TRUE);
	#$this->tz_offset = "+00:00";

    }


    function get_step_count($song_id, $diff=wild) {
	$this->db->where('name', $diff);
	$this->db->where('song_id', $song_id);
	$this->db->select('(perfect1+perfect2+early+late+misses) as notecount');
	$this->db->order_by('score', 'DESC');
	$this->db->limit('1');
	$result = $this->db->get('score')->row()->notecount;
	return $result;

    }

    function get_all_steps($diff) {
echo "<pre>";
	$songs = $this->song_list();
	$steps = Array();
	$song_data = Array();
	foreach($songs as $song){
#		print_r($song);
		$notecount = $steps[$song[$diff]]+$this->get_step_count($song['id'], $diff);
		$song_data[$song[id]] = $song;
		$song_data[$song[id]]['notecount'] = $notecount;
		$steps[$song[$diff]] = $notecount;

		#print_r($steps);
		#exit();
	}
	ksort($steps);
	print_r($steps);
	$total = 0;
	foreach($steps as $step)
		$total = $total+$step;
	echo "Total Steps for ".$diff.": ".$total;

#	print_r($song_data);

#	foreach($song_data as $song){
#		if($song[notecount] > $highest[notecount])
#			$highest=$song;
#	}
#	print_r($highest);

	exit();

    }

    function song_list($id = null)
    {
        if (isset($id)) {
            $this->db->where('id', $id);
        }
		$this->db->order_by('title');
        $query = $this->db->get('song');
        return $query->result_array();
    }


    function user_rank($diff, $first, $last){
		$this->db->where('name', $diff);
		$this->db->join('user', 'on user.id = ranking.user_id');
		$this->db->order_by('rank', 'desc');
		$this->db->where('rank >', 0);
		$this->db->limit($last,$first);
                $this->db->select('ranking.user_id as user_id, rank, name, updated_at, user.username, user.total_score, user.country, user.picture_path');
		$query = $this->db->get('ranking');
		$results =  $query->result_array();
		$users = array();
		foreach ($results as $user){
			if(!$user['country'])
				$user['country'] = "US";
			if(!$user['picture_path'])
				$user['picture_path'] = "uploads/b72a65b1910f794996364e8fdd25216bf84e2bb7.jpg";
			$users[] = $user;
		}
		return $users;
    }

    function user_region_rank($region, $diff, $first, $last){
    	$this->db->where('name', $diff);
    	$this->db->where('user.country', $region);
    	$this->db->join('user', 'on user.id = ranking.user_id');
    	$this->db->order_by('rank', 'desc');
    	$this->db->limit($last,$first);
        $this->db->select('ranking.user_id as user_id, rank, name, updated_at, user.username, user.total_score, user.country, user.picture_path');
    	$query = $this->db->get('ranking');
		$results =  $query->result_array();
		$users = array();
		foreach ($results as $user){
			if(!$user['picture_path'])
				$user['picture_path'] = "uploads/b72a65b1910f794996364e8fdd25216bf84e2bb7.jpg";
			$users[] = $user;
		}
		return $users;
    }


	function user_weekly_rank($diff, $first, $last, $week){
		$start_day = $this->weekly_rank_start($week);
		$this->db->where('start_date', $start_day);
		$this->db->where('name', $diff);
		$this->db->join('user', 'on user.id = weekly_ranking.user_id');
		$this->db->order_by('rank', 'desc');
		$this->db->where('rank >', 0);
		$this->db->limit($last,$first);
		$this->db->select('weekly_ranking.user_id as user_id, rank, name, updated_at, start_date, `0`, `1`, `2`, `3`, `4`, `5`, `6`, user.username, user.total_score, user.country, user.picture_path');
		$query = $this->db->get('weekly_ranking');
		$results =  $query->result_array();
		$users = array();
		foreach ($results as $user){
			$data = array($user['0'], $user['1'],$user['2'],$user['3'],$user['4'],$user['5'],$user['6']);
			$user['graph'] = $data;
			if(!$user['country'])
				$user['country'] = "US";
			if(!$user['picture_path'])
				$user['picture_path'] = "uploads/b72a65b1910f794996364e8fdd25216bf84e2bb7.jpg";
			$users[] = $user;
		}
		return $users;
	}


	function history_scores($first, $last){
                $this->smx->join('song_charts as sc', 'on sc.id = gs.song_chart_id');
		$this->smx->join('songs as s', 'on s.id = sc.song_id');
		$this->smx->join('gamers as g', 'on g.id = gs.gamer_id');
		$this->smx->join('difficulties as d', 'on d.id = sc.difficulty_id');
		$this->smx->order_by('gs.created_at', 'DESC');
		$this->smx->limit($last,$first);
		$this->smx->where('gs.is_enabled', '1');
		$this->smx->select("gs.id as score_id, gs.gamer_id, gs.song_chart_id, gs.score, gs.grade, gs.calories, gs.perfect1, gs.perfect2, gs.early, gs.late, gs.misses, gs.flags, gs.green, gs.yellow, gs.red, CONVERT_TZ(`gs`.`created_at`, '$this->tz_offset', '+00:00') AS `created_at`, s.title, s.artist, s.cover_path,
				   d.name, s.game_song_id, s.subtitle, s.genre, s.label, s.bpm, s.website, s.extra, sc.difficulty, g.username, g.country, g.picture_path");
		$query = $this->smx->get('gamer_scores as gs');
		return $query->result_array();
    }

    function user_scores($userid, $diff, $first, $last){


	if(isset($userid)){
        	if ($diff != 'none'){
                        $this->db->where('name', $diff);
		} else {
			$this->db->where('gamer_id', $userid);
			$this->db->join('song', 'on song.id = score.song_id');
			$this->db->order_by('created_at', 'desc');
			$this->db->limit($last,$first);
			$this->db->select("*, score.id as score_id");
			$query = $this->db->get('score');
			return $query->result_array();
		}
	}

    }

    function user_info($userid){
        $this->db->order_by('total_score', 'DESC');
        $this->db->select("id, total_score");
        $results = $this->db->get('user')->result_array();

        $i = 0;
        foreach($results as $user){
            $i++;
            if($results['user'] == $userid)
                break;
        }

	    $this->db->where('id', $userid);
        $user = $this->db->get('user')->result_array()[0];
        echo  "<pre>";
        print_r($user);
        exit();
    }

    function user_highscores_all($userid, $diff){
	$song_list = $this->song_list();
	$user_info = $this->user_info($userid);

	$this->db->where('name', $diff);
	$this->db->where('gamer_id', $userid);
	$this->db->join('user', 'on user.id = score.gamer_id');
	$this->db->group_by('song_id');
        $this->db->order_by('song_id');
        $this->db->select('song_id, score.id as score_id, user.country, user.picture_path, user.username, max(score) as score, grade, name, title, artist, cover_path, created_at');
        $query = $this->db->get('score');
        $scores = $query->result_array();


        $highscores = Array();
		foreach($scores as $score){
			$highscores[$score['song_id']] = $score;
		}


		$output = Array();

		$song_scores = Array();
		foreach ($song_list as $song){
			$song_id = $song['id'];
			if(!isset($highscores[$song_id])){
				$song_scores[$song_id]['song_id'] = $song_id;
							$song_scores[$song_id]['score'] = "0";
							$song_scores[$song_id]['grade'] = "0";
							$song_scores[$song_id]['name'] = "wild";
							$song_scores[$song_id]['title'] = $song['title'];
							$song_scores[$song_id]['artist'] = $song['artist'];
							$song_scores[$song_id]['cover_path'] = $song['cover_path'];
							$song_scores[$song_id]['created_at'] = "0";
			} else {
				$song_scores[$song_id] = $highscores[$song_id];

			}

		}
		$output['user'] = $user_info;
		$output['scores'] = $song_scores;

		$new = Array();
		foreach($song_scores as $score){
			$new[] = $score;
	}

	return($new);
	//return($song_scores);

    }

    function song_score($id){
    	$this->db->where('score.id', $id);
        $this->db->join('song', 'on song.id = score.song_id');
		$this->db->join('user', 'on user.id = score.gamer_id');
        $this->db->select('*, score.id as score_id');
        $query = $this->db->get('score');
        return $query->result_array();

    }

    function highscores($diff, $first, $last){

		$this->db->where('name', $diff);
		$this->db->order_by('song.title');
		$this->db->join('song', 'on song.id = score.song_id');
        $this->db->join('user', 'on user.id = score.gamer_id');
		$this->db->select('score.id as score_id, user.id as user_id, user.username, song_id, score, '.$diff.',song.title, song.artist');
		$query = $this->db->get('score');
		$scores = $query->result_array();

		$highscores = Array();
		foreach ($scores as $score){
		$song_id = $score['song_id'];
				if(isset($highscores[$song_id])){
						if($highscores[$song_id]['score'] < $score['score'])
								$highscores[$song_id] = $score;
				} else {
						$highscores[$song_id] = $score;
				}
		}
        return $highscores;
    }

    function total_scores($user, $chart){
        if(!is_numeric($chart) || !is_numeric($user))
            return;
        $this->db->where('song_chart_id', $chart);
	$this->db->where('gamer_id', $user);
	$this->db->select('count(*) as count');
	return $this->db->get('score')->row('count');

    }

    function song_user_scorehistory($sort, $chart, $user, $first, $last){
        if(!is_numeric($chart) || !is_numeric($user))
            return;

	if($sort == "date")
            $this->smx->order_by('date', 'desc');
        else
            $this->smx->order_by('score', 'desc');

        $this->smx->where('song_chart_id', $chart);
        $this->smx->where('gamer_id', $user);
        $this->smx->limit($last, $first);
        $this->smx->select("id, gamer_id, score, grade, flags, CONVERT_TZ(`gamer_scores`.`created_at`, '$this->tz_offset', '+00:00') as date");
        $query = $this->smx->get('gamer_scores');
	return $query->result_array();


    }

    function song_scorehistory($id, $diff, $first, $last){
		$this->db->order_by('date', 'desc');
		$this->db->where('song.id', $id);
		$this->db->where('score.name', $diff);
		$this->db->join('song', 'on song.id = score.song_id');
		$this->db->join('user', 'on user.id = score.gamer_id');
        $this->db->limit($last,$first);
		$this->db->select('score.id as score_id, song_id, gamer_id, user.username, user.country, user.picture_path,
                        score, grade, perfect1, perfect2, early, late, misses, flags, green, yellow, red,
                        score.name as diff, '.$diff.' as level, created_at as date');
		$query = $this->db->get('score');
		$scores = $query->result_array();

		return $scores;
    }


    function song_highscores($id) {
		$this->db->where('song.id', $id);
		$this->db->join('song', 'on song.id = score.song_id');
		$this->db->join('user', 'on user.id = score.gamer_id');
		$this->db->select('*,  score.id as score_id');
        $query = $this->db->get('score');
        $scores = $query->result_array();

		$highscores = Array();
        foreach ($scores as $score){
			$diff = $score['name'];
			if(isset($highscores[$diff])){
					if($highscores[$diff]['score'] < $score['score'])
							$highscores[$diff] = $score;
			} else {
					$highscores[$diff] = $score;
			}
        }
        return $highscores;
    }


    function user_highscores($id, $diff) {
		$this->db->where('score.name', $diff);
        $this->db->where('score.gamer_id', $id);
        $this->db->join('song', 'on song.id = score.song_id', 'left');
        $this->db->join('user', 'on user.id = score.gamer_id');
        $this->db->select('score.id as score_id, user.id as user_id, user.username, user.country, user.picture_path, song_id, song.title, song.artist,
			score, grade, perfect1, perfect2, early, late, misses, flags, green, yellow, red,
			score.name as diff, '.$diff.' as level, created_at as date');
        $query = $this->db->get('score');
        $scores = $query->result_array();

		$highscores = Array();
        foreach ($scores as $score){
                $song_id = $score['song_id'];
                if(isset($highscores[$song_id])){
                        if($highscores[$song_id]['score'] < $score['score'])
                                $highscores[$song_id] = $score;
                } else {
                        $highscores[$song_id] = $score;
                }
        }
        return $highscores;
    }

    function user_stats_db($userid, $diff=Null){
		if (isset($diff)) {
			if(is_numeric($diff))
				$diff = $this->diff_convert($diff);
			$userid = $this->db->escape($userid);
			$diff = $this->db->escape($diff);
			$sql = "SELECT grade, count(*) as count FROM `score` WHERE gamer_id=$userid and name = $diff group by grade";
		} else {
			$sql = "SELECT grade, count(*) as count FROM score WHERE gamer_id=$userid group by grade";
		}

		$query = $this->db->query($sql);
		return json_encode($query->result_array());

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
		return json_encode($query->result_array());

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
		$query = $this->db->get_where('song', array('id' => $songid));
		return json_encode($query->row_array());
    }

    function song_scores_by_title_db($title, $artist){
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

    function song_scores_by_id_db($song_id){
        $id = $this->db->escape($song_id);
        $sql = "select * from score
                inner join song
                on song.id = score.song_id
                where score.id==$song_id
                order by score desc";

        $query = $this->db->query($sql);
        return json_encode($query->result_array());


    }

    function user_highscores_title_db($userid, $diff=4){

        $diff = $this->diff_convert($diff);

		$userid = $this->db->escape($userid);
        $diff = $this->db->escape($diff);

        $sql = "select * from score
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


    function getWeeklyRankPoints($user, $type, $last=NULL)
    {
	if($last) {
                date_default_timezone_set('UTC');
		$start_day = gmdate('Y-m-d', strtotime($this->weekly_rank_start().' - 1 week'));
	} else {
        	$start_day = $this->weekly_rank_start();
	}
        $this->db->where('start_date', $start_day);
        $this->db->select('rank');
        $this->db->where('user_id', $user);
        $this->db->where('name', $type);
                $result =  $this->db->get('weekly_ranking')->result_array();
                if(!$result)
                        return 0;
                return $result[0]['rank'];

    }

    function getRankPoints($user, $type)
    {
        $this->db->select('rank');
        $this->db->where('user_id', $user);
        $this->db->where('name', $type);
		$result =  $this->db->get('ranking')->result_array();
		if(!$result)
			return 0;
		return $result[0]['rank'];

    }

    function getWeeklyRank($user, $type, $last=NULL)
    {
        if($last) {
                date_default_timezone_set('UTC');
                $start_day = date('Y-m-d', strtotime($this->weekly_rank_start().' - 1 week'));
        } else {
                $start_day = $this->weekly_rank_start();
	}
        $this->db->where('start_date', $start_day);
        $this->db->select('user.id, rank');
        $this->db->join('user', 'user.id = weekly_ranking.user_id');
        $this->db->where('name', $type);
        $this->db->order_by('rank', 'DESC');
        $results = $this->db->get('weekly_ranking')->result_array();

            $i=0;
            foreach($results as $rank){
                $i++;
                if($rank['id'] == $user)
                    break;
            }
            return $i;

        }

    function getRank($user, $type, $region=null)
	{
		if($region){
			$this->db->where('user.country', $region);
		}
            $this->db->select('user.id, rank');
            $this->db->join('user', 'user.id = ranking.user_id');
            $this->db->where('name', $type);
			$this->db->order_by('rank', 'DESC');

            $results = $this->db->get('ranking')->result_array();

			$i=0;
			foreach($results as $rank){
				$i++;
				if($rank['id'] == $user)
					break;
			}
			return $i;

	}

    function get_ranking_data($diff)
    {
        $this->db->where('name', $diff);
        $this->db->where('rank >', 0);
        $this->db->order_by('rank', 'desc');
        $this->db->join('user', 'user.id = ranking.user_id');
        return $this->db->get('ranking')->result_array();
    }

    function get_region_ranking_data($diff, $country)
        {
            $this->db->where('name', $diff);
            $this->db->where('rank >', 0);
            $this->db->order_by('rank', 'desc');
            $this->db->join('user', 'user.id = ranking.user_id');
            $this->db->where('country', $country);
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


    function getSongData(){
        $songs = $this->db->get('song')->result_array();
	
        $out = array();
        foreach($songs as $song){
            $tempSong['songName'] = $song['title'];
            $tempSong['songArtist'] = $song['artist'];
            if (!is_numeric($song['bpm'])){
                $song['bpm'] = split('-', $song['bpm'])[1];
            }
            $tempSong['bpm'] = floatval($song['bpm']);
            $tempSong['hasDifficulties'] = TRUE;
            $tempSong['hasDiffNumbers'] = TRUE;

            $difficulties = array();
	    $diffs = Array('basic', 'easy', 'hard', 'wild', 'dual', 'full', 'team');
            foreach($diffs as $diff){
                $tempDiff['diffName'] = $diff;
                $tempDiff['diffNumber'] = floatval($song[$diff]);
                if($diff != 'team') $tempDiff['players'] = 1; else $tempDiff['players'] = 2;
                if($diff == 'full' || $diff == 'dual') $tempDiff['doubles'] = TRUE; else $tempDiff['doubles'] = FALSE;
                $difficulties[] = $tempDiff;
            }

            $tempSong['difficulties'] = $difficulties;
            $out[] = $tempSong;
        }
	return $out;
    }


    function getUserSongScore($user, $chart_id){
        $this->db->join('user', 'ON score.gamer_id = user.id');
        $this->db->where('gamer_id', $user);
        $this->db->where('song_chart_id', $chart_id);
        $this->db->select('*, MAX(score) as score');
        $result = $this->db->get('score')->result_array();
	return $result;
    }

    function getUserHighScores($user, $diff){


		$this->db->where('gamer_id = ', $user);
		$this->db->where('name =', $diff);
		$this->db->group_by('title, artist');
		//$this->db->join('user as u', 'gamer_id = u.id');
		$this->db->join('song as s', 's.id = score.song_id');
		$this->db->select('s.id as id, s.game_song_id, gamer_id, score.id as score_id, score.grade, max(score) as score, score.title, s.subtitle, score.artist, score.name, s.cover_path');
		$high_scores = $this->db->get('score')->result_array();

		$song_list = $this->song_list();

		// organize song list by id
		foreach($song_list as $song){
			$songs[$song['game_song_id']] = $song;
		}

		// organize score list by song_id
		foreach($high_scores as $score){
			$scores[$score['game_song_id']] = $score;
		}

		// for songs not played, set score to 0
		foreach($songs as $key=>$song){
			if(array_key_exists($key, $scores))
				$high_score_list[$key] = $scores[$key];
			else {
				$high_score_list[$key]['id'] = $song['id'];
				$high_score_list[$key]['score'] = 0;
				$high_score_list[$key]['score_id'] = 0;
				$high_score_list[$key]['grade'] = -1;
				$high_score_list[$key]['gamer_id'] = $user;
				$high_score_list[$key]['game_song_id'] = $key;
				$high_score_list[$key]['name'] = $diff;
				$high_score_list[$key]['title'] = $song['title'];
				$high_score_list[$key]['subtitle'] = $song['subtitle'];
				$high_score_list[$key]['artist'] = $song['title'];
				$high_score_list[$key]['cover_path'] = $song['cover_path'];

			}
		}

		// convert json objects into an array (remove the keys)
		$high_score_list = array_values($high_score_list);
		return $high_score_list;

	}

	function getRegionHighScores($region, $diff){

    	if($region != 'all')
    		$this->db->where("u.country", $region);
    	$this->db->where('s.name', $diff);
    	$this->db->order_by('s.title, s.created_at');
    	$this->db->join('user as u', 'u.id = s.gamer_id');
    	$this->db->join('song as s1', 's1.title = s.title');
    	$this->db->join('song as s2', 's2.artist = s.artist');
    	$this->db->select("s1.id as id, score, s.id as score_id, s.grade, s.title, s1.subtitle, s.artist, gamer_id, u.username, u.picture_path, u.country,
    						s1.game_song_id, created_at, s.cover_path");
		$all_scores = $this->db->get("score as s")->result_array();


		$high_score_list = Array();

		foreach($all_scores as $score){
			$id = $score['game_song_id'];
			if(empty($high_score_list[$id])) {
				$high_score_list[$id] = $score;
			} elseif($high_score_list[$id]['score'] <= $score['score']) {
				$high_score_list[$id] = $score;
			}
		}

		$song_list = $this->song_list();

		// organize song list by id
		foreach($song_list as $song){
			$songs[$song['game_song_id']] = $song;
		}

		// for songs not played, set score to 0
		foreach($songs as $key=>$song){
			if(!array_key_exists($key, $high_score_list)) {
				$high_score_list[$key]['id'] = $song['id'];
				$high_score_list[$key]['score'] = 0;
				$high_score_list[$key]['score_id'] = 0;
				$high_score_list[$key]['grade'] = -1;
				$high_score_list[$key]['gamer_id'] = 0;
				$high_score_list[$key]['game_song_id'] = $key;
				$high_score_list[$key]['name'] = $diff;
				$high_score_list[$key]['artist'] = $song['title'];
				$high_score_list[$key]['subtitle'] = $song['subtitle'];
				$high_score_list[$key]['title'] = $song['artist'];
				$high_score_list[$key]['subtitle'] = $song['subtitle'];
				$high_score_list[$key]['cover_path'] = $song['cover_path'];
				$high_score_list[$key]['country'] = $region;
			}
		}

		// convert json objects into an array (remove the keys)
		$high_score_list = array_values($high_score_list);
		return $high_score_list;

		return $high_score_list;
	}


}

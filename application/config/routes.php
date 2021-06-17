<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'main';
$route['player/(:num)'] = 'main/scores/$1';
$route['player/(:num)/(:any)'] = 'main/scores/$1/$2';
$route['embed/(:num)'] = 'main/embed/$1';
$route['embed/(:num)/(:any)'] = 'main/embed/$1/$2';
$route['player/(:num)/compare/(:any)/(:any)'] = 'main/rival/$1/$2/$3';
$route['song/(:num)'] = 'main/song/$1';
$route['song/(:num)/(:any)'] = 'main/song/$1/$2';
$route['ranking'] = 'ranking/index/wild';
$route['ranking/(:any)'] = 'ranking/index/$1';
$route['songs'] = 'main/songs';
$route['songs/(:any)'] = 'main/songs/$1';
$route['news'] = 'main/news';
$route['players'] = 'main/users';
$route['search'] = 'main/search';

#api
$route['api/users(:num)/limit/(:num)/(:num)'] = 'api/users/limit/$1/$2';
$route['api/users/(:num)/scores'] = 'api/user_score_history/$1';
$route['api/users/(:num)/scores/limit/(:num)/(:num)'] = 'api/user_score_history/$1/$2/$3';
$route['api/users/(:num)/scores/diff/(:any)'] = 'api/users_scores/$1/$2';
$route['api/users/(:num)/scores/diff/(:any)/limit/(:num)/(:num)'] = 'api/user_scores/$1/$2/$3/$4';
$route['api/highscores/songs/diff/(:any)'] = 'api/highscores/$1';
$route['api/highscores/songs/(:num)'] = 'api/song_highscores/$1';
$route['api/highscores/users/(:num)']  = 'api/user_highscores_all/$1';
$route['api/highscores/users/(:num)/diff/(:any)'] = 'api/user_highscores/$1/$2';
$route['api/scores/songs/(:num)/diff/(:any)'] = 'api/song_scorehistory/$1/$2';
$route['api/scores/songs/(:num)/diff/(:any)/limit/(:num)/(:num)'] = 'api/song_scorehistory/$1/$2/$3/$4';
$route['api/score/(:num)'] = 'api/score/$1';

$route['api/rank/diff/(:any)'] = 'api/rank/$1';
$route['api/rank/diff/(:any)/limit/(:num)/(:num)'] = 'api/rank/$1/$2/$3';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

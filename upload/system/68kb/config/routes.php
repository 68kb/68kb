<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

// Administration
$admin = $this->config->item('admin', 'triggers');
$route[$admin] = "admin/home";
$route[$admin.'/([a-zA-Z_-]+)/(:any)'] = "$1/admin/$2";
$route[$admin.'/(login|logout)'] = "admin/home/$1";
$route[$admin.'/([a-zA-Z_-]+)'] = "$1/admin/index";

// Users
$users = $this->config->item('users', 'triggers');
$route[$users.'/(:any)'] = "users/$1";

// Categories
$cats = $this->config->item('categories', 'triggers');
$route[$cats] = "categories";
$route[$cats.'/(:any)'] = "categories/$1";

$route['article/(:any)'] = "kb/articles/$1";

// Defaults
$route['default_controller'] = "home";
$route['scaffolding_trigger'] = "";

// This is a feature of Modular Separation that sends all 404 to pages module to be handled
$route['404'] = 'pages';

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
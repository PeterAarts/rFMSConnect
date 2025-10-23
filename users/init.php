<?php
session_start();

$abs_us_root=$_SERVER['DOCUMENT_ROOT'];
$self_path=explode("/", $_SERVER['PHP_SELF']);
$self_path_length=count($self_path);
$file_found=FALSE;

for($i = 1; $i < $self_path_length; $i++){
	array_splice($self_path, $self_path_length-$i, $i);
	$us_url_root=implode("/",$self_path)."/";
	
	if (file_exists($abs_us_root.$us_url_root.'z_us_root.php')){
		$file_found=TRUE;
		break;
	}else{
		$file_found=FALSE;
	}
}

require_once $abs_us_root.$us_url_root.'users/helpers/helpers.php';
//error_reporting(E_ALL);
error_reporting(0);
// Set config
$GLOBALS['config'] = array(
'mysql'      => array(
'host'         => ':::1',
'username'     => 'xxx',
'password'     => 'xxx',
'db'           => 'xxx',
),
'remember'        => array(
  'cookie_name'   => 'pmqesoxiw318334575498',
  'cookie_expiry' => 900  // in seconds, One week, feel free to make it longer
),
'session' => array(
  'session_name' => 'user',
  'token_name' => 'token'
)
);



//If you changed your rFMSReader database prefix
//put it here.
$db_table_prefix = "uc_";  //Old database prefix
$timezone_string = 'Europe/Amsterdam';
$copyright_message = 'Peter Aarts 2022';
$your_private_key = '6Lc_MDcUAAAAAGebttI8IiLVnHyImncT9Yp4WSrO';
$your_public_key = '6Lc_MDcUAAAAAC-B1arvynIOLRjJw20HCjzkTgk0';
$publickey = $your_public_key;
$privatekey = $your_private_key;

date_default_timezone_set($timezone_string);

//adding more ids to this array allows people to access everything, whether offline or not. Use caution.
$master_account = [1];

//Put Your o Here (if you have them)
$test_secret = "Insert_Your_Own_Key_Here";
$test_public = "Insert_Your_Own_Key_Here";
$live_secret = "Insert_Your_Own_Key_Here";
$live_public = "Insert_Your_Own_Key_Here";

require_once $abs_us_root.$us_url_root.'users/classes/Config.php';
require_once $abs_us_root.$us_url_root.'users/classes/Cookie.php';
require_once $abs_us_root.$us_url_root.'users/classes/DB.php';
require_once $abs_us_root.$us_url_root.'users/classes/Hash.php';
require_once $abs_us_root.$us_url_root.'users/classes/Input.php';
require_once $abs_us_root.$us_url_root.'users/classes/Redirect.php';
require_once $abs_us_root.$us_url_root.'users/classes/Session.php';
require_once $abs_us_root.$us_url_root.'users/classes/Token.php';
require_once $abs_us_root.$us_url_root.'users/classes/User.php';
require_once $abs_us_root.$us_url_root.'users/classes/Validate.php';
require_once $abs_us_root.$us_url_root.'users/classes/phpmailer/PHPMailerAutoload.php';
require_once $abs_us_root.$us_url_root.'users/classes/Shuttle_Dumper.php';

$currentPage = currentPage();

//Check to see if user has a remember me cookie
if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))){
	$hash = Cookie::get(Config::get('remember/cookie_name'));
	$hashCheck = DB::getInstance()->query("SELECT * FROM users_session WHERE hash = ? AND uagent = ?",array($hash,Session::uagent_no_version()));
	if ($hashCheck->count()) {
		$user = new User($hashCheck->first()->user_id);
		$user->login();
	}
}
//if (!Session::exists(Config::get('session/session_name'))){  header('HTTP/1.0 408 Session-Expired');die();}

//Check to see that user is logged in on a temporary password
$user = new User();

//Check to see that user is verified
if($user->isLoggedIn()){
	if($user->data()->email_verified == 0 && $currentPage != 'verify.php' && $currentPage != 'logout.php' && $currentPage != 'verify_thankyou.php'){
		Redirect::to('users/verify.php');
	}
}

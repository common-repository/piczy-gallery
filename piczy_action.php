<?php
require_once "piczy_api_v1.3.php";

$picture_id = $_GET["picture_id"];
$return_url = $_GET["return_url"];
if ($return_url == ''){
	$return_url = $_POST["pz_return"];
}

$api_id = get_option('pz_api_id');
$api_key = get_option('pz_api_key');

$api = new PiczyApi($api_id,$api_key);

switch ($_GET["action"]){
case 'reply':

	if ($picture_id == '' || !is_numeric($picture_id)){
		die('No picture found.');
	}

	$message = $_POST["message"];
	if ($message != '' && $picture_id != 0){
		$return = $api->do_reply($picture_id,$message);

		$reply = $return->reply;
		$reply->date = date("d-m-Y H:i:s",strtotime($reply->date));
	}

	header("Content-Type: application/json");
	echo json_encode($reply);

	break;
case 'love':

	if ($picture_id == '' || !is_numeric($picture_id)){
		die('No picture found.');
	}

	if ($picture_id != 0){
		$return = $api->do_love($picture_id);
		$loves = array(
			'loves' => $return->loves
		);
	}

	header("Content-Type: application/json");
	echo json_encode($loves);

	break;
case 'logoff':

	$return = $api->user_logoff();

	break;
}
?>
<?php
class PiczyApi{

	private $api_id;
	private $api_key;
	private $api_site;
	private $show_error;

	public function __construct($api_id, $api_key,$show_error = true){
		$this->api_id = $api_id;
		$this->api_key = $api_key;
		$this->show_error = $show_error;
		$this->api_site = 'http://piczy.net/api/';
	}

	public function get_gallery(){
		$data = array(
			'controller' => 'gallery',
			'action' => 'get_gallery'
		);

		return $this->sendRequest($data);
	}

	public function get_groups(){
		$data = array(
			'controller' => 'gallery',
			'action' => 'get_groups'
		);

		return $this->sendRequest($data);
	}

	public function get_pictures($group_id = 0){
		$data = array(
			'controller' => 'gallery',
			'action' => 'get_pictures',
			'postdata' => array(
				'group_id' => $group_id,
			)
		);

		return $this->sendRequest($data);
	}

	public function get_picture($group_id,$picture_id){
		$data = array(
			'controller' => 'gallery',
			'action' => 'get_picture',
			'postdata' => array(
				'group_id' => $group_id,
				'picture_id' => $picture_id
			)
		);

		return $this->sendRequest($data);
	}

	public function get_replies($picture_id){
		$data = array(
			'controller' => 'replies',
			'action' => 'get_replies',
			'postdata' => array(
				'picture_id' => $picture_id
			)
		);

		return $this->sendRequest($data);
	}

	public function do_reply($picture_id,$message){
		$data = array(
			'controller' => 'replies',
			'action' => 'save_reply',
			'postdata' => array(
				'picture_id' => $picture_id,
				'message' => $message
			)
		);

		return $this->sendRequest($data);
	}

	public function do_love($picture_id){
		$data = array(
			'controller' => 'loves',
			'action' => 'save_love',
			'postdata' => array(
				'picture_id' => $picture_id
			)
		);

		return $this->sendRequest($data);
	}

	public function user_online(){
		$data = array(
			'controller' => 'login',
			'action' => 'has_access');

		$return = $this->sendRequest($data);

		return $return;
	}

	public function user_logoff(){
		$data = array(
			'controller' => 'login',
			'action' => 'logoff');

		$return = $this->sendRequest($data);

		return $return;
	}

	public function user_key(){
		$return = $this->user_online();
		if ($return->access){

			$return = new stdClass();
			$return->sid = $_COOKIE['pz_sid'];
			$return->key = $_COOKIE['pz_key'];

		}else{

			$data = array(
				'controller' => 'login',
				'action' => 'get_key',
			);

			$return = $this->sendRequest($data);

			setcookie("pz_sid", $return->sid, time() + (60 * 60 * 24 * 30));
			setcookie("pz_key", $return->key, time() + (60 * 60 * 24 * 30));

		}

		return $return;
	}

	public function sendRequest($data){
		$data = (object)$data;
		$data->ip = $_SERVER["REMOTE_ADDR"];
		$data->agent = $_SERVER["HTTP_USER_AGENT"];
		$data->sid = $_COOKIE['pz_sid'];
		$data->key = $_COOKIE['pz_key'];

		$enc_request = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->api_key, json_encode($data), MCRYPT_MODE_ECB));

		## create the params array, which will
		## be the POST parameters
		$params = array(
			'aid' => $this->api_id,
			'site' => $_SERVER["HTTP_HOST"],
			'enc_request' => $enc_request
		);
		if (is_array($data->postdata)){
			$params = array_merge($params,$data->postdata);
		}

		## initialize and setup the curl handler
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_site . $data->controller);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		## execute the request
		$result = curl_exec($ch);

		## json_decode the result
		$data = (object)json_decode($result);

		if ($this->show_error){
			## check if we're able to json_decode the result correctly
			if( $result == false || isset($data->success) == false ) {
				echo '<pre>';
				print_r($result);
				die('Bad request');
			}

			## if there was an error in the request, throw an exception
			if( $data->success == false ) {
				echo '<pre>';
				print_r($result);
				die($data->error);
			}
		}

		## if everything went great, return the data
		return $data;
	}

}
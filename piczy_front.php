<?php
function piczy_front($atts){
	$group = $atts['group'];

	$api_id = get_option('pz_api_id');
	$api_key = get_option('pz_api_key');

	$picture_size = get_option('pz_picture_size');
	$picture_form = get_option('pz_picture_form');

	$api = new PiczyApi($api_id,$api_key,false);

	if ($group == 'ungrouped'){
		$return = $api->get_pictures(0);
	}else if ($group != ''){
		$group_id = piczy_group_id($group);
		$return = $api->get_pictures($group_id);
	}else{
		$return = $api->get_pictures();
	}

	if ($return->success){

		if ($picture_size == '') $picture_size = '150';
		if ($picture_form == '') $picture_form = 'square';

		$html = '
			<div class="pz_overview">
		';
		foreach ($return->pictures as $picture){

			$return_url = add_query_arg( array( 'open_id' => $picture->id) );
			$html .= '
				<div id="picture' . $picture->id . '" data-group_id="' . $group_id . '" data-picture_id="' . $picture->id . '" data-return_url="http://' . $_SERVER["HTTP_HOST"] . $return_url . '" class="pz_image pz_size_' . $picture_size . ' pz_form_' . $picture_form . '">
					<img src="' . $picture->thumb . '" alt="" />
				</div>
			';

		}
		$html .= '
			</div>
		';

		if ($_GET["open_id"] != 0){
			$html .= '
				<script type="text/javascript">
					jQuery(function(){
						open_image("#picture' . $_GET["open_id"] . '");
					});
				</script>
			';
		}

		return $html;
	}else{
		return 'No pictures found';
	}
}

function piczy_group_id($group_link){
	$api_id = get_option('pz_api_id');
	$api_key = get_option('pz_api_key');

	$api = new PiczyApi($api_id,$api_key,false);
	$return = $api->get_groups();
	if ($return->success){
		foreach ($return->groups as $group){
			if ($group->link == $group_link){
				return $group->id;
			}
		}
	}else{
		return 0;
	}
}
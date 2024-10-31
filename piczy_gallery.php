<?php
function init_gallery(){
	register_setting('piczy_gallery', 'piczy_gallery', 'piczy_gallery_save');
}

function piczy_gallery(){

	$connected = pz_can_connect();
	$groups = pz_get_groups();
	$gallery = pz_get_gallery();
	?>
	<link rel="stylesheet" href="<?php echo plugins_url('piczy-gallery/css/style.css');?>"/>
	<div class="wrap">
		<h2>Piczy gallery settings</h2>
		<?php if ($connected){ ?>
			<p>
				Below you can see the groups from you Piczy gallery. With the tags shown, you can set a gallery at the place you want in your website.
			</p>

			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th class="column-title" width="20%">Group</th>
						<th class="column-title" width="10%" style="text-align: center;">Pictures</th>
						<th class="column-title">Tag</th>
					</tr>
				</thead>

				<tbody id="the-list">
					<?php
					if (!is_array($groups)){
						?>
						<tr>
							<td colspan="3">No groups found.</td>
						</tr>
						<?php
					}else{

						$back = 'alternate';
						foreach ($groups as $group){
							if ($back == ''){
								$back = 'alternate';
							}else{
								$back = '';
							}
							?>
							<tr class="<?php echo $back;?>">
								<td><?php echo $group->name;?></td>
								<td style="text-align: center;"><?php echo $group->pictures;?></td>
								<td>[piczy group="<?php echo $group->link;?>"]</td>
							</tr>
							<?php
						}

					}
					?>
				</tbody>
			</table>

			<br />
			<br />

			<h2>Add a new group?</h2>
			<p>
				If you want to add a new group, go to your gallery in Piczy, and add the group with the pictures u like.<br />
				<a target="_blank" href="<?php echo $gallery->link;?>">Click here to see your gallery.</a>

			</p>
		<?php } else { ?>
			<p>
				<strong>Error:</strong> your API settings aren't setup right.<br />Go to the <a href="admin.php?page=piczy_api_settings">settings page</a> to setup the API settings right.
			</p>
		<?php } ?>
	</div>
	<?php

}

function pz_can_connect(){
	$api_id = get_option('pz_api_id');
	$api_key = get_option('pz_api_key');

	$api = new PiczyApi($api_id,$api_key,false);
	$gallery = $api->get_gallery();

	if ($gallery->success){
		return true;
	}else{
		return false;
	}
}

function pz_get_gallery(){
	$api_id = get_option('pz_api_id');
	$api_key = get_option('pz_api_key');

	$api = new PiczyApi($api_id,$api_key,false);
	$return = $api->get_gallery();
	if ($return->success){
		return $return;
	}else{
		return '';
	}
}

function pz_get_groups(){

	$api_id = get_option('pz_api_id');
	$api_key = get_option('pz_api_key');

	$api = new PiczyApi($api_id,$api_key,false);
	$return = $api->get_groups();
	if ($return->success){
		return $return->groups;
	}else{
		return '';
	}
}
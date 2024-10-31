<?php
function init_settings(){

	register_setting('piczy_settings', 'piczy_settings', 'piczy_settings_save');

	add_settings_section('pz_settings', 'API settings', '', 'pz_plugin');

	add_settings_field('pz_api_id', 'API ID:', 'piczy_field', 'pz_plugin', 'pz_settings',array('name' => 'pz_api_id','size' => '10'));
	add_settings_field('pz_api_key', 'API Key:', 'piczy_field', 'pz_plugin', 'pz_settings',array('name' => 'pz_api_key','size' => '25'));

}



function piczy_settings(){

	?>
	<div class="wrap">
		<h2>Piczy API settings</h2>
		<form action="options.php" method="post">

			<p>
				If you want to use this plugin, you need an account and gallery on <a href="http://piczy.net/">http://piczy.net/</a>.<br />
				Create a gallery, go to the settings page and press 'Generate API key'. You then get an API ID and an API key.<br />
				<br />
				The last thing, fill the field 'API site' with: <strong><?php echo $_SERVER["HTTP_HOST"];?></strong><br />
				<br />
				Fill in the API ID and API Key below.
			</p>
			<?php
			settings_fields('piczy_settings');
			do_settings_sections('pz_plugin');
			?>

			<p class="submit">
				<input name="Submit" type="submit"  class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
		</form>

		<h2>How to use?</h2>
		Go to 'Gallery' in the main menu, here you can create picture groups to show in your website.<br />
		Or you can use the groups you created with Piczy.
	</div>
	<?php

}

function piczy_settings_save(){
	update_option('pz_api_id', $_POST['pz_api_id']);
	update_option('pz_api_key', $_POST['pz_api_key']);
}
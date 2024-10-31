<?php
function init_gallery_settings(){

	register_setting('piczy_gallery_settings', 'piczy_gallery_settings', 'piczy_gallery_settings_save');

	add_settings_section('pz_gallery_settings', 'Picture settings', '', 'pz_gallery_plugin');

	add_settings_field('pz_picture_size', 'Picture size:', 'piczy_size', 'pz_gallery_plugin', 'pz_gallery_settings',array('name' => 'pz_picture_size'));
	add_settings_field('pz_picture_form', 'Picture form:', 'piczy_form', 'pz_gallery_plugin', 'pz_gallery_settings',array('name' => 'pz_picture_form'));

}



function piczy_gallery_settings(){

	?>
	<link rel="stylesheet" href="<?php echo plugins_url('piczy-gallery/css/style.css');?>"/>
	<div class="wrap">
		<h2>Piczy gallery settings</h2>
		<form action="options.php" method="post">

			<p>
				Here you can determine how the pictures will look in your website.
			</p>
			<?php
			settings_fields('piczy_gallery_settings');
			do_settings_sections('pz_gallery_plugin');
			?>

			<p class="submit">
				<input name="Submit" type="submit"  class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
		</form>
	</div>
	<?php

}

function piczy_gallery_settings_save(){
	update_option('pz_picture_size', $_POST['pz_picture_size']);
	update_option('pz_picture_form', $_POST['pz_picture_form']);
}

function piczy_size($param){
	$image = plugins_url('piczy-gallery/img/example_image.jpg');
	$value = get_option($param['name']);
	?>
	<div class="sizes">
		<div data-size="200" class="size <?php if ($value == 200){?>active<?php }?> size_200">
			<img src="<?php echo $image;?>" alt="200x200 pixels">
			<div class="label">200x200 pixels</div>
		</div>
		<div data-size="175" class="size <?php if ($value == 175){?>active<?php }?> size_175">
			<img src="<?php echo $image;?>" alt="175x175 pixels">
			<div class="label">175x175 pixels</div>
		</div>
		<div data-size="150" class="size <?php if ($value == 150){?>active<?php }?> size_150">
			<img src="<?php echo $image;?>" alt="150x150 pixels">
			<div class="label">150x150 pixels</div>
		</div>
		<div data-size="125" class="size <?php if ($value == 125){?>active<?php }?> size_125">
			<img src="<?php echo $image;?>" alt="125x125 pixels">
			<div class="label">125x125 pixels</div>
		</div>
		<div data-size="100" class="size <?php if ($value == 100){?>active<?php }?> size_100">
			<img src="<?php echo $image;?>" alt="100x100 pixels">
			<div class="label">100x100 pixels</div>
		</div>
		<div class="clear"></div>
	</div>
	<input type="hidden" id="<?php echo $param['name'];?>" name="<?php echo $param['name'];?>" value="<?php echo $value;?>" />
	<script type="text/javascript">
		jQuery(".sizes .size").on('click',function(){
			jQuery('.sizes .active').removeClass('active');
			jQuery("#<?php echo $param['name'];?>").val(jQuery(this).data("size"));
			jQuery(this).addClass('active');
		});
	</script>
	<?php
}

function piczy_form($param){
	$image = plugins_url('piczy-gallery/img/example_image.jpg');
	$value = get_option($param['name']);
	?>
	<div class="forms">
		<div data-form="round" class="form size_150 <?php if ($value == 'round'){?>active<?php }?> form_round"><img src="<?php echo $image;?>" alt="Round"></div>
		<div data-form="square" class="form size_150 <?php if ($value == 'square'){?>active<?php }?> form_square"><img src="<?php echo $image;?>" alt="Square"></div>

		<div class="clear"></div>
	</div>
	<input type="hidden" id="<?php echo $param['name'];?>" name="<?php echo $param['name'];?>" value="<?php echo $value;?>" />
	<script type="text/javascript">
		jQuery(".forms .form").on('click',function(){
			jQuery('.forms .active').removeClass('active');
			jQuery("#<?php echo $param['name'];?>").val(jQuery(this).data("form"));
			jQuery(this).addClass('active');
		});
	</script>
	<?php
}
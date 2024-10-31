<?php
$group_id = $_POST["group_id"];
$picture_id = $_POST["picture_id"];
$return_url = $_POST["return_url"];
if ($picture_id == '' || !is_numeric($picture_id)){
	die('No picture found.');
}

$api_id = get_option('pz_api_id');
$api_key = get_option('pz_api_key');

$picture_form = get_option('pz_picture_form');

$api = new PiczyApi($api_id,$api_key);

$return = $api->get_picture($group_id,$picture_id);
if ($return->success){

	$online = $api->user_online();
	if ($online->access){
		$user = $online->user;
		$access = true;
	}else{
		$access = false;
		$keys = $api->user_key();
	}

	$picture = $return->picture;
	$replies = $api->get_replies($picture->id);
	?>
	<div class="pz_picture popform_height">

		<div class="pz_image_large popform_height">
			<?php if ($picture->prev_picture != 0){?><a class="pz_to_picture pz_prev" data-picture_id="<?php echo $picture->prev_picture;?>">&lt;</a><?php } ?>
			<img src="<?php echo $picture->image;?>" alt="" />
			<?php if ($picture->next_picture != 0){?><a class="pz_to_picture pz_next" data-picture_id="<?php echo $picture->next_picture;?>">&gt;</a><?php } ?>
		</div>
		<div class="pz_side popform_height">
			<div class="pz_overflow">
				<?php if ($picture->description != ''){ ?>
					<div class="pz_description">
						<strong>Description:</strong><br />
						<div class="pz_text"><?php echo $picture->description;?></div>
					</div>
				<?php }?>
				<div class="pz_loves">
					<strong>Loves</strong>
					<div class="pz_loving">Loves: <span><?php echo $picture->likes;?></span> <a class="pz_send_love">I Love it</a></div>
				</div>
				<div class="pz_replies">
					<strong>Replies</strong>
					<div id="pz_replies_list">
						<?php
						if (is_array($replies->replies)){

							foreach ($replies->replies as $reply){
								?>
								<div class="pz_reply">
									<div class="pz_avatar pz_form_<?php echo $picture_form;?>"><?php if ($reply->avatar != ''){?><img src="<?php echo $reply->avatar;?>" alt="" /><?php } ?></div>
									<div class="pz_message">
										<strong><?php echo $reply->name;?>:</strong> <?php echo $reply->reply;?><br />
										<small><?php echo date("d-m-Y H:i:s",strtotime($reply->date));?></small>
									</div>
								</div>
								<?php
							}

						}else{
							?>
							<div class="norows">No replies yet.</div>
							<?php
						}
						?>
					</div>
					<div class="pz_reply_form">
						<?php
						if ($access){

							?>
							<form onsubmit="return submit_reply();" id="form-reply" method="post">
								<div class="pz_replyform">
									<div class="pz_field">
										<textarea name="message" id="message" rows="2" cols="50" placeholder="Type a message"></textarea><br />
									</div>
									<div class="pz_user pz_form_<?php echo $picture_form;?>">
										<?php if ($user->avatar != ''){?><img src="<?php echo $user->avatar;?>" height="20" alt="" /><?php } ?>
										<?php if ($false){ ?><strong><?php echo $user->name;?></strong> (<a href="<?php echo plugins_url();?>/piczy/piczy_action.php?action=logoff&picture_id=<?php echo $picture->id;?>&return_url=<?php echo urlencode($return_url);?>">Logoff</a>)<?php } ?>
									</div>
									<div class="pz_right">
										<button class="pz_button" type="submit">Reply</button>
									</div>
									<div class="pz_clear"></div>
								</div>
								<input type="hidden" name="pz_return" id="pz_return" value="<?php echo $return_url;?>" />
							</form>
							<?php

						}else{

							?>
							<div class="pz_login">
								You must login with your Piczy account to reply on this picture.<br />
								<form action="http://www.piczy.net/api/login" method="post">
									<input type="hidden" name="pz_sid" value="<?php echo $keys->sid;?>" />
									<input type="hidden" name="pz_key" value="<?php echo $keys->key;?>" />
									<input type="hidden" name="pz_site" value="<?php echo $_SERVER["HTTP_HOST"];?>" />
									<input type="hidden" name="pz_return" value="<?php echo $return_url;?>" />
									<button class="pz_button" type="submit">Login with Piczy</button>
								</form>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div id="pz_reply_template" class="pz_hide">

		<div class="pz_reply">
			<div class="pz_avatar pz_form_<?php echo $picture_form;?>">{.avatar}</div>
			<div class="pz_message">
				<strong>{.name}:</strong> {.reply}<br />
				<small>{.date}</small>
			</div>
		</div>

	</div>
	<script type="text/javascript">
		jQuery(".pz_to_picture").on('click',function(){
			open_image("#picture" + jQuery(this).data("picture_id"));
		});
		jQuery(".pz_send_love").on('click',function(){
			submit_love()
		});

		function submit_reply(){
			var data = {
				action: 'piczy_action',
				message: jQuery("#message").val(),
				pz_return: jQuery("#pz_return").val()
			};

			jQuery("#message").val('');
			jQuery.post('/wp-admin/admin-ajax.php?action=reply&picture_id=<?php echo $picture->id;?>',data,function(data){


				var html = jQuery("#pz_reply_template").html();
					if (data.avatar != null){
						html = html.replace(/{.avatar}/g,'<img src="' + data.avatar + '" alt="" />');
					}else{
						html = html.replace(/{.avatar}/g,'');
					}
					html = html.replace(/{.name}/g,data.name);
					html = html.replace(/{.reply}/g,data.reply);
					html = html.replace(/{.date}/g,data.date);

				jQuery("#pz_replies_list").append(html);

			});
			return false;
		}

		function submit_love(){
			var data = {
				action: 'piczy_action'
			};

			jQuery("#message").val('');
			jQuery.post('/wp-admin/admin-ajax.php?action=love&picture_id=<?php echo $picture_id;?>',data,function(data){

				if (data.loves != '' && data.loves != null){
					jQuery(".pz_loving span").html(data.loves);
					jQuery(".pz_send_love").remove();
				}

			});
			return false;
		}
	</script>
	<?php
}else{
	?>No picture found<?php
}
?>
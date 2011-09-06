<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php _e('Social Broadcasting Options', 'social'); ?></title>
	<?php
		wp_admin_css('install', true);
		do_action('admin_print_styles');
	?>
</head>
<body>
<h1 id="logo"><?php _e('Social Broadcasting Options', Social::$i18n); ?></h1>
<?php if (count($errors)): ?>
<div id="social_error">
	<?php
		foreach ($errors as $error) {
			echo esc_html($error).'<br />';
		}
	?>
</div>
<?php endif; ?>
<p><?php __('You have chosen to broadcast this blog post to your social accounts. Use the form below to edit your broadcasted messages.', Social::$i18n); ?></p>

<form id="setup" method="post" action="<?php echo esc_url(admin_url('post.php?social_controller=broadcast&social_action=options')); ?>">
<?php wp_nonce_field(); ?>
<input type="hidden" name="post_ID" value="<?php echo $post->ID; ?>" />
<input type="hidden" name="location" value="<?php echo $location; ?>" />
<table class="form-table">
<?php
	foreach ($services as $key => $service) {
		if (isset($_POST['social_'.$key.'_content'])) {
			$content = $_POST['social_'.$key.'_content'];
		}
		else {
			$content = get_post_meta($post->ID, '_social_'.$key.'_content', true);
			if (empty($content)) {
				$content = $service->format_content($post, Social::instance()->option('broadcast_format'));
			}
		}
		$counter = $service->max_broadcast_length();
		if (!empty($content)) {
			$counter = $counter - strlen($content);
		}

		$accounts = $service->accounts();
		$total_accounts = count($accounts);
		$heading = sprintf(__('Publish to %s:', Social::$i18n), ($total_accounts == '1' ? 'this account' : 'these accounts'));

		if ($total_accounts) {
?>
	<tr>
		<th scope="row">
			<label for="<?php echo esc_attr($key.'_preview'); ?>"><?php _e($service->title(), Social::$i18n); ?></label><br/>
			<span id="<?php echo esc_attr($key.'_counter'); ?>" class="social-preview-counter<?php echo ($counter < 0 ? ' social-counter-limit' : ''); ?>"><?php echo $counter; ?></span>
		</th>
		<td>
			<textarea id="<?php echo esc_attr($key.'_preview'); ?>" name="<?php echo esc_attr('social_'.$key.'_content'); ?>" class="social-preview-content" cols="40" rows="5"><?php echo stripslashes((isset($_POST['social_'.$key.'_content'])) ? $_POST['social_'.$key.'_content'] : esc_textarea($content)); ?></textarea><br/>
			<strong><?php echo $heading; ?></strong><br/>
			<?php
				foreach ($accounts as $account):
					$checked = true;
					if ((isset($_POST['social_action']) and $_POST['social_action'] == 'Update' and !isset($_POST['social_'.$key.'_accounts'])) or
					    (isset($_POST['social_'.$key.'_accounts']) and !in_array($account->id().'|true', $_POST['social_'.$key.'_accounts'])) or
						(isset($_POST['social_action']) and !isset($_POST['social_'.$key.'_accounts'])))
					{
						$checked = false;

					}
					else if (count($broadcasted_ids)) {
						if (isset($broadcasted_ids[$key]) and isset($broadcasted_ids[$key][$account->id()])) {
							$checked = false;
						}
						else if ($post->post_status != 'publish' and (!count($broadcast_accounts) or (isset($broadcast_accounts[$key]) and isset($broadcast_accounts[$key][$account->id()])))) {
							$checked = false;
						}
					}
			?>
			<label class="social-broadcastable" for="<?php echo esc_attr($key.$account->id()); ?>" style="cursor:pointer">
				<input type="checkbox" name="<?php echo esc_attr('social_'.$key.'_accounts[]'); ?>" id="<?php echo esc_attr($key.$account->id()); ?>" value="<?php echo esc_attr($account->id().($account->universal() ? '|true' : '')); ?>"<?php echo ($checked ? ' checked="checked"' : ''); ?> />
				<img src="<?php echo esc_attr($account->avatar()); ?>" width="24" height="24" />
				<span><?php echo esc_html($account->name()); ?></span>
			</label>
			<?php endforeach; ?>
		</td>
	</tr>
<?php
		}
	}
?>
</table>
<p class="step">
	<input type="submit" name="social_action" value="<?php _e($step_text, Social::$i18n); ?>" class="button" />
	<a href="<?php echo esc_url(get_edit_post_link($post->ID, 'url')); ?>" class="button">Cancel</a>
</p>
</form>
<script type="text/javascript" src="<?php echo esc_url(includes_url('/js/jquery/jquery.js')); ?>"></script>
<script type="text/javascript" src="<?php echo esc_url(SOCIAL_ADMIN_JS); ?>"></script>
</body>
</html>
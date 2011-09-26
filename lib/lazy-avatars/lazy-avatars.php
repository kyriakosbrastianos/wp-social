<?php
/**
 * Lazy Avatars Plugin
 *
 * This is originally a WordPress plugin. Social includes this as part of it's core to help reduce the
 * page load times with many avatars to display.
 *
 * @author Janis Elsts (http://w-shadow.com/blog/)
 * @version 1.0
 */
final class LazyAvatars {
	private $enabled = false; //Lazy avatars on/off.
	private $wasEnabled = false; //Whether they were on at any point during this page request.
	private $wasUsed = false;

	/**
	 * Sets up the filters and actions.
	 */
	public function __construct() {
		add_action('enable_lazy_avatars', array($this, 'enable'));
		add_filter('get_avatar', array($this, 'makeAvatarLazy'), 30, 5);
		add_action('wp_footer', array($this, 'footer'));
	}

	/**
	 * Creates the placeholder for the avatar.
	 *
	 * @param  string  $avatar
	 * @param  string  $id_or_email
	 * @param  string  $size
	 * @param  string  $default
	 * @param  string  $alt
	 * @return string
	 */
	public function makeAvatarLazy($avatar, $id_or_email, $size = '32', $default = '', $alt = '') {
		if (!$this->enabled) {
			return $avatar;
		}

		// Extract the image source
		if (preg_match('@src=(["\'])([^"\']+)\1?@i', $avatar, $matches)) {
			$src = esc_attr($matches[2]);

			// Extract CSS classes
			$classes = '';
			if (preg_match('@class=(["\'])([^"\']+)\1?@i', $avatar, $matches)) {
				$classes = ' '.$matches[2];
			}

			// Extract width
			$width = '';
			if (preg_match('@width=(["\'])([^"\']+)\1?@i', $avatar, $matches)) {
				$width = $matches[2];
			}

			// Extract height
			$height = '';
			if (preg_match('@height=(["\'])([^"\']+)\1?@i', $avatar, $matches)) {
				$height = $matches[2];
			}

			$avatar = sprintf(
				'<div class="lazy-avatar pending%s" data-avatar-src="%s" data-avatar-width="%s" data-avatar-height="%s"></div>',
				esc_attr($classes),
				esc_attr($src),
				esc_attr($width),
				esc_attr($height)
			);
			$this->wasUsed = true;
		}
		return $avatar;
	}

	/**
	 * Enables LazyAvatars.
	 *
	 * @param  bool  $enabled
	 * @return void
	 */
	public function enable($enabled = true) {
		$this->enabled = (bool) $enabled;
		if ($this->enabled) {
			$this->wasEnabled = true;
		}
	}

	/**
	 * Adds the assets to the footer.
	 *
	 * @return void
	 */
	public function footer() {
		if ($this->wasEnabled and $this->wasUsed) {
			$this->printStyle();
			$this->printScript();
		}
	}

	/**
	 * Prints the JavaScript content.
	 *
	 * @return void
	 */
	public function printScript() {
		$scriptFile = dirname(__FILE__).'/js/avatar-loader.min.js';
		echo '<script>';
		readfile($scriptFile);
		echo '</script>';
	}

	/**
	 * Prints the CSS content.
	 *
	 * @return void
	 */
	public function printStyle() {
		echo '<style>.lazy-avatar { display: inline-block }</style>';
	}
}

$ws_lazy_avatars = new LazyAvatars();

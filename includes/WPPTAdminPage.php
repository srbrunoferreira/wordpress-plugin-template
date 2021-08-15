<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * The class responsible for provide the visual elements of the plugin.
 * These elements can be menu links and setting pages.
 *
 * @author Bruno Ferreira <srbrunoferreira@outlook.com>
 * @version 1.1.0
 * @license GPL-3.0
 * @link https://github.com/srbrunoferreira/WordPress-Plugin-Settings-API-Template
 * @see https://github.com/pinoceniccola/WordPress-Plugin-Settings-API-Template
 */
class WPPTAdminPAGE
{
	private $file;
	private $plugin_name;
	private $plugin_slug;
	private $textdomain;
	private $options;
	private $settings;

	public function __construct($plugin_name, $plugin_slug, $file)
	{
		$this->file = $file;
		$this->plugin_slug = $plugin_slug;
		$this->plugin_name = $plugin_name;
		$this->textdomain = str_replace('_', '-', $plugin_slug);

		add_action('admin_init', [$this, 'init']); // Initialise settings
		add_action('admin_menu', [$this, 'add_menu_item']); // Add settings page to menu

		add_filter('plugin_action_links_' . plugin_basename($this->file), [$this, 'add_settings_link']);
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init()
	{
		$this->settings = $this->settings_fields();
		$this->options = $this->get_options();
		$this->register_settings();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item()
	{
		$page = add_options_page($this->plugin_name, $this->plugin_name, 'manage_options', $this->plugin_slug, [$this, 'settings_page']);
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link($links)
	{
		$settings_link = '<a href="options-general.php?page=' . $this->plugin_slug . '">' . __('Settings', $this->textdomain) . '</a>';
		array_push($links, $settings_link);

		return $links;
	}

	/**
	 * Here, the setting fields are build.
	 *
	 * Tested fields: text, password, text_secret, textarea,
	 * checkbox, select, radio, checkbox_multi, number, select_multi
	 *
	 * @return array Fields to be displayed on settings page.
	 */
	private function settings_fields()
	{
		$settings['easy'] = [
			'title'					=> __('Standard', $this->textdomain),
			'description'			=> __('These are fairly standard form input fields.', $this->textdomain),
			'fields'				=> [
				[
					'id' 			=> 'text_field',
					'label'			=> __('Some Text', $this->textdomain),
					'description'	=> __('This is a standard text field.', $this->textdomain),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __('Placeholder text', $this->textdomain),
				],
				[
					'id' 			=> 'password_field',
					'label'			=> __('A Password', $this->textdomain),
					'description'	=> __('This is a standard password field.', $this->textdomain),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __('Placeholder text', $this->textdomain),
				]
			]
		];

		$settings = apply_filters('plugin_settings_fields', $settings);

		return $settings;
	}


	/**
	 * Options getter
	 * @return array Options, either saved or default ones.
	 */
	public function get_options()
	{
		$options = get_option($this->plugin_slug); // Current database

		// Checks if a hardcoded field was removed and then resets the database options.
		if (!$options && is_array($this->settings)) { // $this->settings === $code settings field
			$options = [];
			echo '<hr>OPTIONS<hr>';
			echo '<pre>';
			print_r($options);
			echo '</pre><hr>';
			echo '<hr>THIS->SETTINGS<hr>';
			echo '<pre>';
			print_r($this->settings);
			echo '</pre><hr>';

			foreach ($this->settings as $section => $data) {
				foreach ($data['fields'] as $field) {
					$options[$field['id']] = $field['default'];
				}
			}

			add_option($this->plugin_slug, $options);
		}

		return $options;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings()
	{
		if (is_array($this->settings)) {
			register_setting($this->plugin_slug, $this->plugin_slug, [$this, 'validate_fields']);

			foreach ($this->settings as $section => $data) {

				// Add section to page
				add_settings_section($section, $data['title'], [$this, 'settings_section'], $this->plugin_slug);

				foreach ($data['fields'] as $field) {

					// Add field to page
					add_settings_field($field['id'], $field['label'], [$this, 'display_field'], $this->plugin_slug, $section, ['field' => $field]);
				}
			}
		}
	}

	public function settings_section($section)
	{
		$html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field($args)
	{
		$field = $args['field'];

		$html = '';

		$option_name = $this->plugin_slug . '[' . $field['id'] . ']';

		$data = (isset($this->options[$field['id']])) ? $this->options[$field['id']] : '';

		switch ($field['type']) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '"/>' . "\n";

				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value=""/>' . "\n";

				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '">' . $data . '</textarea><br/>' . "\n";

				break;

			case 'checkbox':
				$checked = '';
				if ($data && 'on' == $data) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" ' . $checked . '/>' . "\n";

				break;

			case 'checkbox_multi':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if (is_array($data) && in_array($k, $data)) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
				}

				break;

			case 'radio':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if ($k == $data) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
				}

				break;

			case 'select':
				$html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if ($k == $data) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
				}
				$html .= '</select> ';

				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" multiple="multiple">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if (in_array($k, $data)) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';

				break;

			case 'checkbox_multi':
				break;
			case 'radio':
				break;
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';

				break;

			default:
				$html .= '<label for="' . esc_attr($field['id']) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";

				break;
		}

		echo $html;
	}

	/**
	 * Validate individual settings field
	 * @param  array $data Inputted value
	 * @return array       Validated value
	 */
	public function validate_fields($data)
	{
		// $data array contains values to be saved:
		// either sanitize/modify $data or return false
		// to prevent the new options to be saved

		// Sanitize fields, eg. cast number field to integer
		// $data['number_field'] = (int) $data['number_field'];

		// Validate fields, eg. don't save options if the password field is empty
		// if ( $data['password_field'] == '' ) {
		// 	add_settings_error( $this->plugin_slug, 'no-password', __('A password is required.', $this->textdomain), 'error' );
		// 	return false;
		// }

		return $data;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page()
	{
		// Build page HTML output
		// If you don't need tabbed navigation just strip out everything between the <!-- Tab navigation --> tags.
?>
		<div class="wrap" id="<?php echo $this->plugin_slug; ?>">
			<h2><?php _e('My Plugin Settings', $this->textdomain); ?></h2>
			<p><?php _e('Some infos about my plugin.', $this->textdomain); ?></p>

			<!-- Tab navigation starts -->
			<h2 class="nav-tab-wrapper settings-tabs hide-if-no-js">
				<?php
				foreach ($this->settings as $section => $data) {
					echo '<a href="#' . $section . '" class="nav-tab">' . $data['title'] . '</a>';
				} ?>
			</h2>
			<?php $this->do_script_for_tabbed_nav(); ?>
			<!-- Tab navigation ends -->

			<form action="options.php" method="POST">
				<?php settings_fields($this->plugin_slug); ?>
				<div class="settings-container">
					<?php do_settings_sections($this->plugin_slug); ?>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Print jQuery script for tabbed navigation
	 * @return void
	 */
	private function do_script_for_tabbed_nav()
	{
		// Very simple jQuery logic for the tabbed navigation.
		// Delete this function if you don't need it.
		// If you have other JS assets you may merge this there.
	?>
		<script>
			jQuery(document).ready(function($) {
				var headings = jQuery('.settings-container > h2, .settings-container > h3');
				var paragraphs = jQuery('.settings-container > p');
				var tables = jQuery('.settings-container > table');
				var triggers = jQuery('.settings-tabs a');

				triggers.each(function(i) {
					triggers.eq(i).on('click', function(e) {
						e.preventDefault();
						triggers.removeClass('nav-tab-active');
						headings.hide();
						paragraphs.hide();
						tables.hide();

						triggers.eq(i).addClass('nav-tab-active');
						headings.eq(i).show();
						paragraphs.eq(i).show();
						tables.eq(i).show();
					});
				})

				triggers.eq(0).click();
			});
		</script>
<?php
	}
}

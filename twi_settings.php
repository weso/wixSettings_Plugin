<?php
/* 	Plugin Name: TWI Settings
	Plugin URI: 
	Description: Helper for wix wordpress theme setting
	Version: 1.0
	Author: Borja Garrido
	Author URI:
	License: GPLv2 or later
*/

if (isset($_POST["Submit"], $_POST["path"])) {
        if (file_exists($_POST["path"])) {
                file_put_contents($_POST["path"], json_encode($_POST["json"]));
        }
}

function twi_settings_activation() {
	if (!file_exists('settings.json')) {
		fopen('settings.json', 'w');
	}
}
register_activation_hook(__FILE__, 'twi_settings_activation');

function twi_settings_deactivation() {
	if (file_exists('settings.json')) {
		unlink('settings.json');
	}
}
register_deactivation_hook(__FILE__, 'twi_settings_deactivation');

add_action('admin_menu', 'twi_plugin_settings');
function twi_plugin_settings() {
	
	add_menu_page('TWI Settings Page', 'TWI Settings Page', 'administrator', 'twi_settings', 'twi_display_settings');
}

function admin_register_head() {
    $style = plugins_url( 'style/style.css', __FILE__);
    echo "<link rel='stylesheet' type='text/css' href='$style' />\n";
}
add_action('admin_head', 'admin_register_head');

function twi_display_settings() {
	$setting_files = load_settings_json_files();

	$html = '</pre>
	<main class="wrap">
		<h1>The web index settings</h1>
		<nav class="tabs"><ul>';
	
	foreach ($setting_files as $name => $path) {
		$html .= '<li>'.$name.'</li>';
	}

	$html .= '</ul></nav>
		<section class="panels">';
		
	foreach ($setting_files as $name => $path) {
		$content = file_get_contents($path);
		$html .= '<div><form id="twi-settings-'. strtolower($name) . '-form" action="" method="post" name="options">';
		$json = json_decode($content, True);
		
		if ($json) {
			foreach ($json as $key => $value) {
				if (is_array($value)) {
					$html .= '<div class="field-container">';
					foreach ($value as $sub_key => $sub_value) {
						$html .= '<label for="'.$key.'_'.$sub_key.'">'.$sub_key.'</label>';
						$html .= '<input type="text" name="json['.$key.']['.$sub_key.']" id="'.$key.'_'.$sub_key.'" value="'.$sub_value.'"><br>';
					}
					$html .= '</div>';	
				} else {
					$html .= '<label for="'.$key.'">'.$key.'</label>';
					if ($key == 'password') {
						$html .= '<input type="password" name="json['.$key.']" id="'.$key.'" value="'.$value.'"><br>';
					} else {
						$html .= '<input type="text" name="json['.$key.']" id="'.$key.'" value="'.$value.'"><br>';
					}		
				}
			}
		}
		$html .= '<input type="hidden" name="path" value="'.$path.'"/>';	
		$html .= '<input type="submit" name="Submit" value="Update" /></form>';

		if ($name == 'Visualisations') {
			$html .= '<button type="button" onClick="appendField(\''. strtolower($name). '\')">Add fields</button>';
		}

		$html .= '</div>';
	}
		
	$html .='</section></main>';
	
	$script = plugins_url( 'js/script.js', __FILE__);
	$html .= '<script src="'. $script. '"/>';

	echo $html;
}

function load_settings_json_files() {
    	$settings = plugin_dir_path( __FILE__)  . 'settings.json';

	$wix_theme = get_stylesheet_directory();
	
	$json_files = Array();
		
	if (file_exists($settings)) {
		$settings_content = file_get_contents($settings);
		$json = json_decode($settings_content, true);

		$json_settings = $json['paths'];
		foreach ($json_settings as $settings_file) {

			if (file_exists($wix_theme.$settings_file['path'])) {
				$json_files[$settings_file['name']] = $wix_theme.$settings_file['path'];
			}
		}
	} else {
		echo '<div class="error">Plugin settings not found</div>';
	}
	
	return $json_files;
}
?>

<?php
namespace SR_PDF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Form_Dynamic_Fields {

	public function __construct() {
		add_filter('elementor_pro/forms/field_value', [ $this, 'fill_user_select_field' ], 10, 2);
	}

	public function fill_user_select_field($field_value, $field_name) {
		if ('dynamic_users' !== $field_value) return $field_value;

		$users = get_users();
		$options = [];

		foreach ($users as $user) {
			$name = $user->display_name;
			$unique_id = get_user_meta($user->ID, 'sr_unique_id', true);

			if ($unique_id) {
				$options[] = "$name ($unique_id)";
			}
		}

		return implode('|', $options); // Elementor separa opciones con "|"
	}
}

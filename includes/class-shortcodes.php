<?php
namespace SR_PDF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Shortcodes {

	public function __construct() {
		add_shortcode('user_select_dropdown', [ $this, 'render_user_select' ]);
	}

	public function render_user_select() {
		$users = get_users();
		ob_start();
		?>
		<select id="user-select-dropdown" name="project_manager_id" required style="width: 100%; padding: 6px; font-size: 14px;">
			<option value="">— Selecciona un usuario —</option>
			<?php foreach ($users as $user): 
				$id = get_user_meta($user->ID, 'sr_unique_id', true);
				if ($id): ?>
					<option value="<?php echo esc_attr($id); ?>">
						<?php echo esc_html($user->display_name . " ($id)"); ?>
					</option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
		<?php
		return ob_get_clean();
	}
}

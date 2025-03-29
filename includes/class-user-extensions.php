<?php
// ==============================
// Smart Referrals: ID + Position
// ==============================

// Mostrar campos personalizados en el perfil de usuario
add_action('show_user_profile', 'sr_add_user_fields');
add_action('edit_user_profile', 'sr_add_user_fields');

function sr_add_user_fields($user) {
	$user_id = $user->ID;
	$role = $user->roles[0] ?? '';
	$user_unique_id = get_user_meta($user_id, 'sr_unique_id', true);
	$project_position = get_user_meta($user_id, 'sr_project_manager_position', true);

	// Generar ID único si no existe
	if (!$user_unique_id) {
		do {
			$user_unique_id = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
		} while (sr_id_exists($user_unique_id));

		update_user_meta($user_id, 'sr_unique_id', $user_unique_id);
	}

	?>
	<h2>Smart Referrals - Identificación</h2>
	<table class="form-table">
		<tr>
			<th><label for="sr_unique_id">ID único</label></th>
			<td>
				<input type="text" name="sr_unique_id" id="sr_unique_id" value="<?php echo esc_attr($user_unique_id); ?>" class="regular-text" readonly />
				<p class="description">Este ID de 5 dígitos identifica al usuario como <?php echo ($role === 'administrator') ? 'Project Manager' : 'Cliente'; ?>.</p>
			</td>
		</tr>

		<?php if ($role === 'administrator') : ?>
		<tr>
			<th><label for="sr_project_manager_position">Cargo del Project Manager</label></th>
			<td>
				<select name="sr_project_manager_position" id="sr_project_manager_position">
					<option value="">— Seleccionar —</option>
					<option value="Project Manager" <?php selected($project_position, 'Project Manager'); ?>>Project Manager</option>
					<option value="UI Designer" <?php selected($project_position, 'UI Designer'); ?>>UI Designer</option>
					<option value="Full Stack Developer" <?php selected($project_position, 'Full Stack Developer'); ?>>Full Stack Developer</option>
					<option value="Senior Developer" <?php selected($project_position, 'Senior Developer'); ?>>Senior Developer</option>
					<option value="Líder de Proyecto" <?php selected($project_position, 'Líder de Proyecto'); ?>>Líder de Proyecto</option>
				</select>
				<p class="description">Este campo solo es visible para usuarios con rol de administrador.</p>
			</td>
		</tr>
		<?php endif; ?>
	</table>
	<?php
}

// Guardar campos personalizados al actualizar usuario
add_action('personal_options_update', 'sr_save_user_fields');
add_action('edit_user_profile_update', 'sr_save_user_fields');

function sr_save_user_fields($user_id) {
	if (current_user_can('edit_user', $user_id)) {
		if (isset($_POST['sr_project_manager_position'])) {
			update_user_meta($user_id, 'sr_project_manager_position', sanitize_text_field($_POST['sr_project_manager_position']));
		}
	}
}

// Verificar si el ID generado ya existe
function sr_id_exists($id) {
	$args = [
		'meta_key'   => 'sr_unique_id',
		'meta_value' => $id,
		'number'     => 1,
		'fields'     => 'ID',
	];
	$users = get_users($args);
	return !empty($users);
}

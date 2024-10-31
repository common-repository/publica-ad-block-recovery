<section class="wrap">
	<h2>
		<?php echo esc_html( $this->plugin->displayName ); ?> &raquo; Settings
	</h2>
	<?php if ( isset( $this->error_message ) ) { ?>
		<div class="error">
			<p><?php echo esc_html( $this->error_message ); ?></p>
		</div>
	<?php } ?>
	<?php if ( isset( $this->message ) ) { ?>
		<div class="updated">
			<p><?php echo esc_html( $this->message ); ?></p>
		</div>
	<?php } ?>

	<?php if ( $this->settings['publica_script_installed'] ) { ?>
		<h3>Publica Bootloader Script has already been installed</h3>
		<p>Would you like to check for updates?</p>
		<button class="button button-primary"
			onclick="document.getElementById('pb-login').style.display = '';">
			Update!
		</button>
	<?php } else { ?>
		<h3>Publica Bootloader Script</h3>
		<p>Please login below to install the Publica Ad Recovery script.</p>
	<?php } ?>
	<form
		id="pb-login"
		style="<?php echo $this->settings['publica_script_installed'] ? 'display: none;' : ''; ?>"
		action="options-general.php?page=<?php echo rawurlencode( $this->plugin->name ); ?>"
		method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						Username
					</th>
					<td>
						<input
							type="text"
							class="form-control"
							id="pb-username"
							name="username"
							placeholder="Username">
					</td>
				</tr>
				<tr>
					<th scope="row">
						Password
					</th>
					<td>
						<input
							type="password"
							class="form-control"
							id="pb-passwd"
							name="passwd"
							placeholder="Password">
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input
				type="submit"
				name="submit"
				class="button button-primary"
				value="Login">
		</p>
		<?php wp_nonce_field( $this->plugin->name, $this->plugin->name . '_nonce' ); ?>
	</form>
</section>

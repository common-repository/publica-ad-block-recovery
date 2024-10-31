<?php
/**
* Plugin Name: Publica Ad Block Recovery
* Plugin URI:
* Version: 1.0.0
* Author: Publica
* Author URI: https://getpublica.com/
* Description: Helping Publishers monetize ad blocked traffic through video ad reinsertion.
* License: GPLv2
*/

/**
* Publica Ad Block Recovery plugin
*/
class PublicaAdRecovery {
	public function __construct() {
		$this->plugin               = new stdClass;
		 // Plugin Folder
		$this->plugin->name         = 'publica-ad-block-recovery';
		 // Plugin Name
		$this->plugin->displayName  = 'Publica Ad Recovery';
		$this->plugin->version      = '1.0.0';
		$this->plugin->folder       = plugin_dir_path( __FILE__ );
		$this->plugin->url          = plugin_dir_url( __FILE__ );

		$this->publica_api_hostname = 'https://' . (defined( 'PUBLICA_HOST' ) ? constant( 'PUBLICA_HOST' ) : 'api.getpublica.com');

		// Hooks
		add_action( 'admin_init',
			array( &$this, 'admin_init' )
		);
		add_action( 'admin_menu',
			array( &$this, 'admin_menu' )
		);

		// Frontend Hooks
		add_action( 'wp_head', array( &$this, 'frontend_header' ), 0 );
	}

	/**
	* Register Settings
	*/
	function admin_init() {
		register_setting(
			$this->plugin->name,
			'publica_script',
			'trim'
		);
	}

	/**
	* Register the plugin settings panel
	*/
	function admin_menu() {
		add_submenu_page(
			'options-general.php',
			$this->plugin->displayName,
			$this->plugin->displayName,
			'manage_options',
			$this->plugin->name,
			array( &$this, 'admin_panel' )
		);
	}

	/**
	* Output the Administration Panel
	* Save POSTed data from the Administration Panel into a WordPress option
	*/
	function admin_panel() {
		// Save Settings
		if ( isset( $_POST['submit'] ) ) { // Input var okay.
			// Check nonce
			if ( ! isset( $_POST[ $this->plugin->name . '_nonce' ] ) ) { // Input var okay.
				// Missing nonce
				$this->error_message = __( 'Settings NOT saved. (nonce field is missing)', 'publica-ad-block-recovery' );
			} elseif ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $this->plugin->name . '_nonce' ] ) ), $this->plugin->name ) ) { // Input var okay.
				// Invalid nonce
				$this->error_message = __( 'Settings NOT saved. (nonce invalid)', 'publica-ad-block-recovery' );
			} elseif ( ! isset( $_POST['username'] ) || '' === $_POST['username'] || ! isset( $_POST['passwd'] ) || '' === $_POST['passwd'] ) { // Input var okay.
				$this->error_message = __( 'Settings NOT saved. (login form invalid)', 'publica-ad-block-recovery' );
			} else {
				$user = sanitize_text_field( wp_unslash( $_POST['username'] ) ); // Input var okay.
				$passwd = sanitize_text_field( wp_unslash( $_POST['passwd'] ) ); // Input var okay.

				$resp = $this->request_embed_script( $user, $passwd );
				if ( null !== $resp ) {
		    		update_option( 'publica_script', $resp->code );
		    		$this->message = __( 'Publica script was installed.', 'publica-ad-block-recovery' );
				}
			}
		} elseif ( isset( $_POST['clear'] ) ) { // Input var okay.
			delete_option( 'publica_script' );
		}

		// Get latest settings
		$this->settings = array();
		$script = get_option( 'publica_script' );
		$this->settings['publica_script_installed'] = $script;

		// Load Settings Form
		include_once( WP_PLUGIN_DIR . '/' . $this->plugin->name . '/views/settings.php' );
	}

	function request_publica( $url, $body ) {
		$body = wp_remote_retrieve_body( wp_remote_post( $url, array(
			'blocking' => true,
			'headers' => array(
				'authorization' => 'Basic dGVzdF9jbGllbnRfMTp0ZXN0X3NlY3JldA==',
				'content-type' => 'application/x-www-form-urlencoded',
			),
			'body' => $body,
		) ) );
		if ( '' === $body ) {
			return null;
		}
		return json_decode( $body );
	}

	function request_embed_script( $user, $passwd ) {
		$auth = $this->request_publica( $this->publica_api_hostname . '/v1/oauth/tokens', array(
			'grant_type' => 'password',
			'scope' => 'read_write',
			'username' => $user,
			'password' => $passwd,
		) );
		if ( null === $auth ) {
			$this->error_message = __( 'Network error. Please try again at a later time.', 'publica-ad-block-recovery' );
			return null;
		}
		if ( isset( $auth->error ) ) {
			$this->error_message = __( 'Authentication Error. Username or password was incorrect', 'publica-ad-block-recovery' );
			return null;
		}

		return $this->request_publica( $this->publica_api_hostname . '/v1/signup/get_code_default', array(
			'access_token' => rawurlencode( $auth->access_token ),
			'script_tag' => 'false',
		) );
	}

	/**
	* Outputs script / CSS to the frontend header
	*/
	function frontend_header() {
		// @codingStandardsIgnoreLine
		echo '<script type="text/javascript">' . get_option( 'publica_script' ) . '</script>';
	}
}

function publica_deactivate() {
	delete_option( 'publica_script' );
}

register_deactivation_hook( __FILE__, 'publica_deactivate' );

$publica_ad_recovery = new PublicaAdRecovery();

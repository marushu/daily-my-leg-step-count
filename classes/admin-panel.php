<?php

class Hibou_Post_Type_Admin_Panel {

	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		register_activation_hook( DAILY_MY_LEG_STEP_COUNT_PATH, 'activate' );

	}

	public function activate() {

		$options = get_option( 'daily_my_leg_steps' );
		var_dump( $options );

		if ( empty( $options ) ) {
			$default_value = array(
				'key_leg'          => array(),
				'start_step_count' => '',
			);
			update_option( 'daily_my_leg_steps', $default_value );
		}

	}

	/**
	 * Add admin menu
	 */
	public function admin_menu() {
		add_options_page(
			'Step count',
			'Step count',
			'manage_options',
			'step_count',
			array( $this, 'post_notifier_options_page' )
		);
	}

	/**
	 * Register settings.
	 */
	public function settings_init() {

		register_setting(
			'step_count_page_group',
			'daily_my_leg_step_count_settings',
			''
		);

		add_settings_section(
			'step_count_section',
			__( 'Daily my leg step count settings', 'daily-my-leg-step-count' ),
			array( $this, 'step_count_section_section_callback' ),
			'step_count_page'
		);

		add_settings_field(
			'start_step_count',
			__( 'Set start step count.', 'daily-my-leg-step-count' ),
			array( $this, 'first_step_count_render' ),
			'step_count_page',
			'step_count_section'
		);

	}

	/**
	 * Add description of Post Notifier.
	 */
	public function step_count_section_section_callback() {

		echo esc_attr__( 'Set start step and genger and so on. :)', 'daily-my-leg-step-count' );

	}

	public function first_step_count_render() {

	    $options = get_option( 'daily_my_leg_steps' );
	    var_dump( __FILE__ );
	    var_dump( DAILY_MY_LEG_STEP_COUNT_PATH );

		$options = get_option( 'daily_my_leg_steps' );
		var_dump( $options );

	    ?>

        テスト

        <?php

    }

	/**
	 * Output Post Notifier page form.
	 */
	public function post_notifier_options_page() {

		?>
		<form action='options.php' method='post'>

			<?php
			settings_fields( 'step_count_page_group' );
			do_settings_sections( 'step_count_page' );
			submit_button();
			?>

		</form>
		<?php
	}

}
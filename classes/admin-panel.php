<?php

class Hibou_Post_Type_Admin_Panel {

	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );

		register_activation_hook( __FILE__, array( $this::activate() ) );

	}

	public function activate() {

		$daily_my_leg_step_count_settings = get_option( 'daily_my_leg_step_count_settings' );

		if ( empty( $daily_my_leg_step_count_settings ) ) {

			$default_value = array(
				'start_step_count'   => 0,
                'remain_step_count'  => 0,
                'rheoknee_watchword' => '',
			);
			update_option( 'daily_my_leg_step_count_settings', $default_value );
			update_option( 'daily_my_leg_remain_count', 0 );

		}

	}

	/**
	 * Load style
	 */
	public function load_admin_scripts( $hook_suffix ) {
		if ( false === strpos( $hook_suffix, 'step_count' ) )
			return;

		wp_enqueue_style(
			'step_count-admin',
			DAILY_MY_LEG_STEP_COUNT_URL . 'css/style.css',
			array(),
			'',
			'all'
		);

	}

	public function check_fields( $input ) {

		$new_input = array();
		$watchword = '';
		$step_count = '';
	    $options = get_option( 'daily_my_leg_step_count_settings' );
        $rheoknee_watchword = $input[ 'rheoknee_watchword' ];
		$start_step_count = $input[ 'start_step_count' ];

		if ( empty( $rheoknee_watchword ) ) {

			add_settings_error(
				'daily_my_leg_step_count_settings',
				'rheoknee_watchword',
				__( 'Watchword is required. Please set it.', 'daily-my-leg-step-count' ),
				'error'
			);
			$new_input['rheoknee_watchword'] = isset( $options['rheoknee_watchword'] )
                ? $watchword
                : '';

		} else {

		    $new_input[ 'rheoknee_watchword' ] = $input[ 'rheoknee_watchword' ];

        }

        if ( empty( $start_step_count ) || $start_step_count < 0 ) {

		    $new_input[ 'start_step_count' ] = 0;

        } else {

		    $new_input[ 'start_step_count' ] = $input[ 'start_step_count' ];

        }

		return $new_input;

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
			array( $this, 'check_fields' )
		);

		add_settings_section(
			'step_count_section',
			__( 'Daily my leg step count settings', 'daily-my-leg-step-count' ),
			array( $this, 'daily_my_leg_step_count_settings_section_callback' ),
			'step_count_page'
		);

		add_settings_field(
			'start_step_count',
			__( 'Set start step count.', 'daily-my-leg-step-count' ),
			array( $this, 'first_step_count_render' ),
			'step_count_page',
			'step_count_section'
		);

		add_settings_field(
            'rheoknee_watchword',
            __( 'Set your own watchword.', 'daily-my-leg-step-count' ),
            array( $this, 'rheoknee_watchword_render' ),
			'step_count_page',
            'step_count_section'
        );

	}

	/**
	 * Add description of Post Notifier.
	 */
	public function daily_my_leg_step_count_settings_section_callback() {

		echo esc_attr__( 'Set start step and genger and so on. :)', 'daily-my-leg-step-count' );

	}

	public function first_step_count_render() {

	    $options = get_option( 'daily_my_leg_step_count_settings' );
	    $start_step_count = isset( $options[ 'start_step_count' ] )
            ? $options[ 'start_step_count' ]
            : 0;

	    ?>

        <input class="daily_num" type="number" name="daily_my_leg_step_count_settings[start_step_count]"
               value="<?php echo esc_html( $start_step_count ); ?>" size="10" maxlength="30">

        <?php

    }

    public function rheoknee_watchword_render() {

	    $options = get_option( 'daily_my_leg_step_count_settings' );
	    $rheoknee_watchword = isset( $options[ 'rheoknee_watchword' ] )
            ? $options[ 'rheoknee_watchword' ]
            : '';
        ?>

        <input type="text" name="daily_my_leg_step_count_settings[rheoknee_watchword]"
               value="<?php echo esc_html( $rheoknee_watchword ); ?>" size="50" maxlength="30">

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

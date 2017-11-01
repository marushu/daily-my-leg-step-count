<?php

class Hibou_Post_Types {

	function __construct() {

		add_action( 'plugin_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'wpcf7_before_send_mail', array( $this, 'contact_to_post' ) );
		add_action( 'step', array( $this, 'short_code' ) );
		add_filter( 'widget_text', array( $this, 'short_code' ) );

	}

	public function load_textdomain() {

		load_plugin_textdomain(
			'hibou_post_type',
			false,
			DAILY_MY_LEG_STEP_COUNT_DIR . '/languages'
		);
	}

	public function contact_to_post( $cf7 ) {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {

			$option = get_option( 'daily_my_leg_step_count_settings' );

			$rheoknee_steps = isset( $_POST[ 'rheoknee_steps' ] )
				? $_POST[ 'rheoknee_steps' ]
				: 0;

			$rheoknee_watchword = isset( $_POST[ 'rheoknee_watchword' ] )
				? $_POST[ 'rheoknee_watchword' ]
				: '';

			$watchword = $option[ 'rheoknee_watchword' ];

			$post_title = 'Today\'s steps ' . date_i18n( 'Y/m/d' ) . "\n";

			$post = array(
				'post_title'    => esc_html( $post_title ),
				'post_content'  => wp_strip_all_tags( intval( $rheoknee_steps ) ),
				'post_status'   => 'publish',
				'post_type'     => 'daily-leg-steps',
				'post_author'   => '1',
			);

			if ( esc_html( $rheoknee_watchword ) === esc_html( $watchword ) ) {
				$id = wp_insert_post( $post );
			}

			$dir = trailingslashit(  wpcf7_upload_dir( 'dir' ) . '/wpcf7_uploads' );
			$result = $this::coco_list_files( $dir );

			$start_step_count = $option[ 'start_step_count' ];

			$daily_my_leg_remain_count = get_option( 'daily_my_leg_remain_count' );
			var_dump( $daily_my_leg_remain_count );

			if ( intval( $daily_my_leg_remain_count ) === 0 ) {

				$todays_step = intval( $rheoknee_steps ) - intval( $start_step_count );
				update_option( 'daily_my_leg_remain_count', $todays_step );
				var_dump( 'カウントがゼロ' );

			} else {

				$todays_step = intval( $rheoknee_steps ) - intval( $daily_my_leg_remain_count );
				var_dump( $rheoknee_steps );
				var_dump( $daily_my_leg_remain_count );
				var_dump( $todays_step );

				update_option( 'daily_my_leg_remain_count', intval( $rheoknee_steps ) );
				var_dump( 'カウントがゼロじゃないよ？' );

			}

			if ( isset( $todays_step ) && ! is_null( $todays_step ) )
				update_post_meta( $id, 'todays_step', $todays_step );

			if ( ! is_null( $rheoknee_steps ) )
				update_post_meta( $id, 'total_step', $rheoknee_steps );

			if( isset( $_FILES[ 'rheoknee_image' ][ 'name' ] ) && ! empty( $_FILES[ 'rheoknee_image' ][ 'name' ] ) ) {

				$source           = $result[ 0 ];
				$image_exif       = @exif_read_data( $source  );
				$image_orientation = $image_exif[ 'Orientation' ];

				// upload file ( jpg )
				$image_name_datas = $_FILES[ 'rheoknee_image' ];
				$image_name       = $image_name_datas[ 'name' ];
				$uploads_dir      = wpcf7_upload_tmp_dir();
				$uploads_dir      = trailingslashit( wpcf7_upload_tmp_dir() );
				$image_location   = trailingslashit( $uploads_dir ) . $image_name_datas[ 'name' ];

				$image = wp_get_image_editor( $source );
				if ( ! is_wp_error( $image ) ) {
					switch ( $image_orientation ) {
						case 2:
							$image->rotate( 0 );
							break;
						case 3:
							$image->rotate( 180 );
							break;
						case 4:
							$image->rotate( 0 );
							break;
						case 5:
							$image->rotate( -90 );
							break;
						case 6:
							$image->rotate( -90 );
							break;
						case 7:
							$image->rotate( -90 );
							break;
						case 8:
							$image->rotate( 90 );
							break;
					}
					$image->resize( 1400, '', true );
					$saved = $image->save( $image_location );
				}
				$content          = file_get_contents( $saved[ 'path' ] );
				$wud              = wp_upload_dir();
				$upload           = wp_upload_bits( $image_name, '', $content );
				$chemin_final     = $upload[ 'url' ];
				$custom_filename  = $upload[ 'file' ];

				require_once( ABSPATH . 'wp-admin/includes/admin.php' );
				$wp_filetype      = wp_check_filetype( basename( $custom_filename ), null );
				$attachment       = array(
					'post_type'     => 'attachment',
					'post_mime_type'=> $wp_filetype[ 'type' ],
					'post_title'    => sanitize_file_name( preg_replace( '/\.[^.]+$/', '', basename( $custom_filename ) ) ),
					'post_content'  => '',
					'post_status'   => 'inherit',
				);

				$attach_id = wp_insert_attachment( $attachment, $custom_filename, $id );

				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $custom_filename );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				update_post_meta( $id, '_thumbnail_id', $attach_id );
				add_post_meta( $id, 'coco_image', $image_name );
			}

		}

	}

	public function coco_list_files( $dir ) {

		$list = array();
		$files = scandir( $dir );
		foreach( $files as $file ) {
			if( $file == '.' || $file == '..' || $file == '.htaccess' ) {
				continue;
			} else if ( is_file( $dir . $file ) ) {
				$list[] = $dir . $file;
			} else if( is_dir( $dir . $file ) ) {
				$list = array_merge( $list, $this::coco_list_files( $dir . $file . DIRECTORY_SEPARATOR ) );
			}
		}

		return $list;
	}

	public function short_code() {

		$args = array(
			'post_type'      => 'daily-leg-steps',
			'posts_per_page' => 1,
		);
		$step_posts = get_posts( $args );
		if ( empty( $step_posts ) ) {
			update_option( 'daily_my_leg_remain_count', 0 );
			return;
		}

		$step_posts  = reset( $step_posts );
		$todays_step = get_post_meta( $step_posts->ID, 'todays_step', true );
		$total_step  = get_post_meta( $step_posts->ID, 'total_step', true );

		$html  = '';
		$html .= '<div class="step_wrap">' . "\n";

		$html .= '<div class="total_step">' . "\n";
		$html .= '<p class="step_content">Total: ' . intval( $total_step ) . '</p>' . "\n";
		$html .= '</div>' . "\n";

		$html .= '<div class="todays_step">' . "\n";
		$html .= '<p class="step_content">Today: ' . intval( $todays_step ) . '</p>' . "\n";
		$html .= '</div>' . "\n";
		$html .= '</div>' . "\n";

		return $html;

	}

}

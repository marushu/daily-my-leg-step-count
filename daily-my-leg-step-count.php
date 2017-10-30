<?php
/**
 * Plugin Name:     Daily my leg step count
 * Plugin URI:
 * Description:     Add daily step count as post.
 * Author:          Hibou
 * Author URI:      https://private.hibou-web.com
 * Text Domain:     daily-my-leg-step-count
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Daily_My_Leg_Step_Count
 */

define( 'DAILY_MY_LEG_STEP_COUNT_VER', '0.5.0' );
define( 'DAILY_MY_LEG_STEP_COUNT_PATH', trailingslashit( dirname( __FILE__) ) );
define( 'DAILY_MY_LEG_STEP_COUNT_DIR', trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );
define( 'DAILY_MY_LEG_STEP_COUNT_URL', plugin_dir_url( dirname( __FILE__ ) ) . DAILY_MY_LEG_STEP_COUNT_DIR );

require_once( DAILY_MY_LEG_STEP_COUNT_PATH . 'classes/daily-my-leg-step-count-core.php' );
if ( class_exists( 'Hibou_Post_Types' ) )
	$hibou_post_type = new Hibou_Post_Types();

require_once ( DAILY_MY_LEG_STEP_COUNT_PATH . 'classes/admin-panel.php' );
if ( class_exists( 'Hibou_Post_Type_Admin_Panel' ) )
	$hibou_post_type_admin_panel = new Hibou_Post_Type_Admin_Panel();

require_once( DAILY_MY_LEG_STEP_COUNT_PATH . 'post-types/daily-rheoknee-steps.php' );

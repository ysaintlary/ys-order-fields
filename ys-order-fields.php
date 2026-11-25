<?php
/**
 * Plugin Name:       YS | Order Fields
 * Plugin URI:        https://github.com/ysaintlary/ys-order-fields
 * Description:       Ajoute des champs personnalisés aux commandes WooCommerce, saisis au checkout et consultables/modifiables en administration.
 * Version: 1.0.1
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Requires Plugins:  woocommerce
 * Author:            Yves Saint-Lary
 * Author URI:        https://ysaintlary.com
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ys-order-fields
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YS_ORDER_FIELDS_VERSION', '1.0.0' );
define( 'YS_ORDER_FIELDS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once __DIR__ . '/lib/wp-plugin-base/wp-plugin-base-runtime-updater.php';

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'cart_checkout_blocks',
			__FILE__,
			true
		);
	}
} );

add_action( 'plugins_loaded', function () {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	require_once YS_ORDER_FIELDS_PLUGIN_DIR . 'includes/fields.php';
	require_once YS_ORDER_FIELDS_PLUGIN_DIR . 'includes/class-checkout-blocks.php';
	require_once YS_ORDER_FIELDS_PLUGIN_DIR . 'includes/class-checkout-classic.php';
	require_once YS_ORDER_FIELDS_PLUGIN_DIR . 'includes/class-admin.php';
	require_once YS_ORDER_FIELDS_PLUGIN_DIR . 'includes/class-display.php';

	YS_Order_Fields_Checkout_Blocks::init();
	YS_Order_Fields_Checkout_Classic::init();
	YS_Order_Fields_Admin::init();
	YS_Order_Fields_Display::init();
} );

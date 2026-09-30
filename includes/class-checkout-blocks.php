<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YS_Order_Fields_Checkout_Blocks {

	public static function init() {
		add_action( 'woocommerce_init', array( __CLASS__, 'register_fields' ) );
		add_action( 'woocommerce_set_additional_field_value', array( __CLASS__, 'sync_meta' ), 10, 4 );
	}

	public static function register_fields() {
		if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
			return;
		}

		foreach ( ys_order_fields_get_fields() as $field ) {
			$args = array(
				'id'       => 'ys-order-fields/' . $field['key'],
				'label'    => $field['label'],
				'location' => 'order',
				'type'     => self::resolve_type( $field['type'] ),
				'required' => $field['required'],
			);

			if ( ! empty( $field['optional_label'] ) ) {
				$args['optionalLabel'] = $field['optional_label'];
			}

			if ( 'text' === $field['type'] && ! empty( $field['max_length'] ) ) {
				$args['attributes'] = array(
					'maxLength' => $field['max_length'],
				);
			}

			if ( 'select' === $field['type'] && ! empty( $field['options'] ) ) {
				$args['options'] = $field['options'];
			}

			woocommerce_register_additional_checkout_field( $args );
		}
	}

	/**
	 * When WooCommerce saves an additional field value, copy it to the plain meta key.
	 *
	 * @param string  $key       Field identifier (e.g. "ys-order-fields/bt13").
	 * @param mixed   $value     The value being saved.
	 * @param string  $group     "billing", "shipping", or "other".
	 * @param WC_Data $wc_object The order or customer object.
	 */
	public static function sync_meta( $key, $value, $group, $wc_object ) {
		if ( 0 !== strpos( $key, 'ys-order-fields/' ) ) {
			return;
		}

		if ( ! $wc_object instanceof WC_Order ) {
			return;
		}

		$meta_key = substr( $key, strlen( 'ys-order-fields/' ) );
		$wc_object->update_meta_data( $meta_key, $value, true );
		$wc_object->save_meta_data();
	}

	private static function resolve_type( $type ) {
		if ( 'date' === $type && ! self::supports_date_type() ) {
			return 'text';
		}
		return $type;
	}

	private static function supports_date_type() {
		if ( ! defined( 'WC_VERSION' ) ) {
			return false;
		}
		return version_compare( WC_VERSION, '11.2.0', '>=' );
	}
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YS_Order_Fields_Checkout_Classic {

	public static function init() {
		add_filter( 'woocommerce_checkout_fields', array( __CLASS__, 'add_fields' ) );
		add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order', array( __CLASS__, 'save_fields' ), 10, 2 );
	}

	/**
	 * @param array $checkout_fields
	 * @return array
	 */
	public static function add_fields( $checkout_fields ) {
		foreach ( ys_order_fields_get_fields() as $field ) {
			$wc_field = array(
				'label'    => $field['label'],
				'required' => $field['required'],
				'type'     => self::map_type( $field['type'] ),
				'class'    => array( 'form-row-wide' ),
				'priority' => 200,
			);

			if ( 'text' === $field['type'] && ! empty( $field['max_length'] ) ) {
				$wc_field['maxlength'] = $field['max_length'];
			}

			if ( 'select' === $field['type'] && ! empty( $field['options'] ) ) {
				$wc_field['options'] = self::build_select_options( $field['options'], $field['required'] );
			}

			$checkout_fields['order'][ 'ys_order_fields_' . $field['key'] ] = $wc_field;
		}

		return $checkout_fields;
	}

	/**
	 * @param array    $data
	 * @param WP_Error $errors
	 */
	public static function validate( $data, $errors ) {
		foreach ( ys_order_fields_get_fields() as $field ) {
			$posted_key = 'ys_order_fields_' . $field['key'];
			$value      = isset( $_POST[ $posted_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $posted_key ] ) ) : '';

			if ( $field['required'] && '' === $value ) {
				$errors->add(
					'validation',
					sprintf(
						/* translators: %s: field label */
						__( '%s est un champ obligatoire.', 'ys-order-fields' ),
						'<strong>' . esc_html( $field['label'] ) . '</strong>'
					)
				);
			}

			if ( 'text' === $field['type'] && ! empty( $field['max_length'] ) && mb_strlen( $value ) > $field['max_length'] ) {
				$errors->add(
					'validation',
					sprintf(
						/* translators: 1: field label, 2: max length */
						__( '%1$s ne doit pas dépasser %2$d caractères.', 'ys-order-fields' ),
						'<strong>' . esc_html( $field['label'] ) . '</strong>',
						$field['max_length']
					)
				);
			}
		}
	}

	/**
	 * @param WC_Order $order
	 * @param array    $data
	 */
	public static function save_fields( $order, $data ) {
		foreach ( ys_order_fields_get_fields() as $field ) {
			$posted_key = 'ys_order_fields_' . $field['key'];
			$value      = isset( $_POST[ $posted_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $posted_key ] ) ) : '';

			if ( '' === $value ) {
				continue;
			}

			if ( 'checkbox' === $field['type'] ) {
				$value = '1';
			}

			$order->update_meta_data( $field['key'], $value, true );
			$order->update_meta_data( '_wc_other/ys-order-fields/' . $field['key'], $value, true );
		}
	}

	private static function map_type( $type ) {
		$map = array(
			'text'     => 'text',
			'select'   => 'select',
			'checkbox' => 'checkbox',
			'date'     => 'date',
		);
		return isset( $map[ $type ] ) ? $map[ $type ] : 'text';
	}

	/**
	 * @param array $options
	 * @param bool  $required
	 * @return array
	 */
	private static function build_select_options( $options, $required ) {
		$wc_options = array();
		if ( ! $required ) {
			$wc_options[''] = __( 'Sélectionner…', 'ys-order-fields' );
		}
		foreach ( $options as $option ) {
			$wc_options[ $option['value'] ] = $option['label'];
		}
		return $wc_options;
	}
}

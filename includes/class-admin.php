<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YS_Order_Fields_Admin {

	private static $nonce_action = 'ys_order_fields_save';
	private static $nonce_name   = '_ys_order_fields_nonce';

	public static function init() {
		add_action( 'woocommerce_admin_order_data_after_order_details', array( __CLASS__, 'render_fields' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( __CLASS__, 'save_fields' ), 10, 2 );
		add_filter( '__experimental_woocommerce_blocks_checkout_order_processed_additional_fields_response', array( __CLASS__, 'suppress_native_display' ) );
	}

	/**
	 * @param WC_Order $order
	 */
	public static function render_fields( $order ) {
		$fields = ys_order_fields_get_fields();
		if ( empty( $fields ) ) {
			return;
		}

		wp_nonce_field( self::$nonce_action, self::$nonce_name );
		?>
		<div class="order_data_column" style="clear:both; padding-top:20px;">
			<h3><?php esc_html_e( 'Champs personnalisés', 'ys-order-fields' ); ?></h3>
			<?php foreach ( $fields as $field ) : ?>
				<?php
				$value = $order->get_meta( $field['key'], true );
				$id    = 'ys_order_fields_' . $field['key'];
				?>
				<p class="form-field form-field-wide">
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
					<?php if ( $field['admin_editable'] ) : ?>
						<?php self::render_input( $field, $id, $value ); ?>
					<?php else : ?>
						<span><?php echo esc_html( $value ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $field['description'] ) ) : ?>
						<span class="description"><?php echo esc_html( $field['description'] ); ?></span>
					<?php endif; ?>
				</p>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * @param int            $order_id
	 * @param WC_Order|WP_Post $post_or_order
	 */
	public static function save_fields( $order_id, $post_or_order = null ) {
		if ( ! isset( $_POST[ self::$nonce_name ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::$nonce_name ] ) ), self::$nonce_action ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		foreach ( ys_order_fields_get_fields() as $field ) {
			if ( ! $field['admin_editable'] ) {
				continue;
			}

			$posted_key = 'ys_order_fields_' . $field['key'];

			if ( 'checkbox' === $field['type'] ) {
				$value = isset( $_POST[ $posted_key ] ) ? '1' : '0';
			} else {
				$value = isset( $_POST[ $posted_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $posted_key ] ) ) : '';
			}

			$order->update_meta_data( $field['key'], $value, true );
			$order->update_meta_data( '_wc_other/ys-order-fields/' . $field['key'], $value, true );
		}

		$order->save_meta_data();
	}

	/**
	 * @param array $fields
	 * @return array
	 */
	public static function suppress_native_display( $fields ) {
		foreach ( ys_order_fields_get_fields() as $field ) {
			unset( $fields[ 'ys-order-fields/' . $field['key'] ] );
		}
		return $fields;
	}

	private static function render_input( $field, $id, $value ) {
		switch ( $field['type'] ) {
			case 'select':
				?>
				<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>">
					<option value=""><?php esc_html_e( 'Sélectionner…', 'ys-order-fields' ); ?></option>
					<?php foreach ( $field['options'] as $option ) : ?>
						<option value="<?php echo esc_attr( $option['value'] ); ?>" <?php selected( $value, $option['value'] ); ?>>
							<?php echo esc_html( $option['label'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<?php
				break;

			case 'checkbox':
				?>
				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" value="1" <?php checked( $value, '1' ); ?> />
				<?php
				break;

			default:
				$attrs = '';
				if ( ! empty( $field['max_length'] ) ) {
					$attrs = ' maxlength="' . intval( $field['max_length'] ) . '"';
				}
				?>
				<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $attrs; ?> />
				<?php
				break;
		}
	}
}

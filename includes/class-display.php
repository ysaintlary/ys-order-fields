<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YS_Order_Fields_Display {

	public static function init() {
		add_filter( 'woocommerce_email_order_meta_fields', array( __CLASS__, 'email_fields' ), 10, 3 );
		add_action( 'woocommerce_order_details_after_order_table', array( __CLASS__, 'my_account_fields' ) );
	}

	/**
	 * @param array    $fields
	 * @param bool     $sent_to_admin
	 * @param WC_Order $order
	 * @return array
	 */
	public static function email_fields( $fields, $sent_to_admin, $order ) {
		foreach ( ys_order_fields_get_fields() as $field ) {
			$value = $order->get_meta( $field['key'], true );
			if ( '' === $value ) {
				continue;
			}
			$fields[ 'ys_' . $field['key'] ] = array(
				'label' => $field['label'],
				'value' => $value,
			);
		}
		return $fields;
	}

	/**
	 * @param WC_Order $order
	 */
	public static function my_account_fields( $order ) {
		$rows = array();
		foreach ( ys_order_fields_get_fields() as $field ) {
			$value = $order->get_meta( $field['key'], true );
			if ( '' === $value ) {
				continue;
			}
			$rows[] = array(
				'label' => $field['label'],
				'value' => $value,
			);
		}

		if ( empty( $rows ) ) {
			return;
		}
		?>
		<h2><?php esc_html_e( 'Informations complémentaires', 'ys-order-fields' ); ?></h2>
		<table class="woocommerce-table shop_table ys-order-fields-table">
			<tbody>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<th><?php echo esc_html( $row['label'] ); ?></th>
						<td><?php echo esc_html( $row['value'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}

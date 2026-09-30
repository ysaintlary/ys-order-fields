<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the registered order fields.
 *
 * @return array[]
 */
function ys_order_fields_get_fields() {
	$fields = array(
		array(
			'key'            => 'bt13',
			'label'          => __( 'Référence de commande client', 'ys-order-fields' ),
			'optional_label' => __( 'Optionnel', 'ys-order-fields' ),
			'type'           => 'text',
			'required'       => false,
			'max_length'     => 100,
			'options'        => array(),
			'admin_editable' => true,
			'description'    => __( 'Numéro du bon de commande émis par l\'acheteur (BT-13, norme EN 16931).', 'ys-order-fields' ),
		),
	);

	return apply_filters( 'ys_order_fields_fields', $fields );
}

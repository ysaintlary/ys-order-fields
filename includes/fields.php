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
	$fields = array();

	return apply_filters( 'ys_order_fields_fields', $fields );
}

<?php
/**
 * YS Order Fields uninstall handler.
 *
 * Ne supprime JAMAIS les données des commandes (obligation de conservation des factures).
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

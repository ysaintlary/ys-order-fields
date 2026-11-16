=== YS | Order Fields ===
Contributors: ysaintlary
Tags: woocommerce, checkout, order, custom fields, invoice
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Ajoute des champs personnalisés aux commandes WooCommerce, saisis au checkout et consultables/modifiables en administration.

== Description ==

YS Order Fields permet d'ajouter des champs personnalisés aux commandes WooCommerce. Les champs sont saisis par le client sur la page de paiement (checkout en blocs ou classique) et sont consultables et modifiables dans l'écran de commande en administration.

= Fonctionnalités =

* Registre de champs centralisé et filtrable
* Compatible checkout en blocs et checkout classique
* Champs modifiables dans l'écran de commande en administration
* Affichage dans les e-mails de commande et dans « Mon compte »
* Compatible HPOS (High-Performance Order Storage)
* Les données sont accessibles via l'API REST WooCommerce (meta_data)

= Champ inclus =

* **BT-13** : référence de commande client (norme de facture électronique EN 16931)

== Installation ==

1. Télécharger le fichier ZIP du plugin
2. Dans WordPress, aller dans Extensions > Ajouter > Téléverser une extension
3. Sélectionner le fichier ZIP et cliquer sur « Installer maintenant »
4. Activer le plugin

== Changelog ==

= 1.0.1 =
* Fix - fix: update load_config.sh to support WORDPRESS_TEST_PLUGINS key.
* Update - chore: configure GitHub release workflow with wp-plugin-base v1.10.15.
* Update - chore: enable readiness/quality/security packs with PUC exclusions.
* Add - feat: add email and my-account display.
* Add - feat: add admin order screen fields.
* Add - feat: add classic checkout integration.
* Add - feat: add block checkout integration.
* Add - feat: add field registry with BT-13 field.
* Add - feat: scaffold plugin with wp-plugin-base v1.10.0 (local profile).


= 1.0.0 =
* Initial release
* Registre de champs centralisé avec le champ BT-13
* Support du checkout en blocs et classique
* Champs modifiables en administration
* Affichage dans les e-mails et « Mon compte »

# YS Order Fields

Extension WooCommerce ajoutant des champs personnalisés aux commandes.

## Architecture

```
ys-order-fields.php           Fichier principal, chargement, déclaration HPOS/blocs
includes/
  fields.php                  Registre centralisé des champs (source unique de vérité)
  class-checkout-blocks.php   Intégration checkout en blocs (API additionalCheckoutFields)
  class-checkout-classic.php  Intégration checkout classique (shortcode)
  class-admin.php             Affichage/édition dans l'écran de commande admin
  class-display.php           Affichage dans e-mails et « Mon compte »
uninstall.php                 Ne supprime jamais les données de commande
```

## Règle fondamentale : tout passe par le registre

La fonction `ys_order_fields_get_fields()` dans `includes/fields.php` est la source unique de définition des champs. Tout le code (checkout, admin, e-mails) lit ce registre. Ajouter un champ ne demande qu'une nouvelle entrée dans le tableau.

## Ajouter un champ

Ouvrir `includes/fields.php` et ajouter une entrée au tableau `$fields` :

```php
[
    'key'            => 'mon_champ',
    'label'          => __( 'Mon champ', 'ys-order-fields' ),
    'optional_label' => __( 'Optionnel', 'ys-order-fields' ),
    'type'           => 'text',        // text | select | checkbox | date (WC 11.2+)
    'required'       => false,
    'max_length'     => 255,           // ignoré pour checkbox/select
    'options'        => [],            // pour select : [ ['value' => 'a', 'label' => 'A'], ... ]
    'admin_editable' => true,
    'description'    => __( 'Description affichée en admin.', 'ys-order-fields' ),
],
```

Propriétés :
- `key` : clé meta finale sur la commande (source de vérité pour l'API REST)
- `label` : libellé affiché au checkout et en admin
- `optional_label` : texte affiché quand le champ n'est pas requis
- `type` : text, select, checkbox, date (date requiert WooCommerce 11.2+)
- `required` : booléen
- `max_length` : longueur maximale (type text uniquement)
- `options` : liste de valeurs pour le type select
- `admin_editable` : si true, le champ est modifiable en admin
- `description` : aide affichée en administration

Le registre est filtrable : `apply_filters( 'ys_order_fields_fields', $fields )`.

## Double meta et synchronisation

Chaque champ est stocké sous deux clés meta sur la commande :
- `{key}` : source de vérité, lue par l'API REST et Orderinvoicer
- `_wc_other/ys-order-fields/{key}` : clé interne WooCommerce blocs

La synchronisation est bidirectionnelle et automatique.

## Versions minimales

- WordPress 6.5+
- WooCommerce 9.0+
- PHP 7.4+

## Compatibilité

- **HPOS** : déclaré compatible, utilise exclusivement `$order->get_meta()` / `$order->update_meta_data()` / `$order->save()`
- **Checkout blocs** : via `woocommerce_register_additional_checkout_field` (WC 8.9+)
- **Checkout classique** : via `woocommerce_checkout_fields` / `woocommerce_checkout_create_order`
- **Ne jamais** utiliser `get_post_meta` / `update_post_meta` pour les commandes

## Tests manuels (préproduction)

1. **Commande avec BT-13** (checkout blocs) : saisir une valeur, vérifier qu'elle apparaît dans l'écran admin de la commande
2. **Commande sans BT-13** : ne pas remplir le champ, vérifier qu'il n'y a pas d'erreur et que la meta est absente
3. **Commande via checkout classique** : même vérification que 1 et 2
4. **Modification en admin** : modifier la valeur BT-13, sauvegarder, vérifier que les deux meta (`bt13` et `_wc_other/ys-order-fields/bt13`) sont synchronisées
5. **API REST** : `GET /wp-json/wc/v3/orders/{id}` → vérifier que `bt13` apparaît dans `meta_data`
6. **E-mail de commande** : vérifier que BT-13 est affiché si rempli
7. **Mon compte** : vérifier que BT-13 est affiché dans le détail de la commande
8. **Pas de doublon admin** : vérifier que le champ n'est pas affiché deux fois (notre panel + l'affichage natif WC)

## wp-plugin-base

Ce projet utilise wp-plugin-base v1.10.0 avec le profil local (`AUTOMATION_PROFILE=local`).

Commandes locales utiles :
```bash
bash .wp-plugin-base/scripts/ci/lint_php.sh        # Lint PHP
bash .wp-plugin-base/scripts/ci/build_zip.sh        # Construire le ZIP
bash .wp-plugin-base/scripts/ci/check_versions.sh 1.0.0  # Vérifier les versions
```

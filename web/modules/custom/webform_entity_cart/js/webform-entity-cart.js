/**
 * @file
 * Webform Entity Cart behaviors.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.webformEntityCartUpdate = {
    attach (context, settings) {
      $(once('cart-update', '.js-cart-update', context))
        .on('change', Drupal.behaviors.webformEntityCartUpdate.onChange);
    },
    onChange: function (event) {
      // Update EntityCart webform element.
      Drupal.ajax({
        url: this.dataset.url,
        submit: {
          count: parseInt(this.value)
        }
      }).execute();
    }
  };

} (jQuery, Drupal, drupalSettings));

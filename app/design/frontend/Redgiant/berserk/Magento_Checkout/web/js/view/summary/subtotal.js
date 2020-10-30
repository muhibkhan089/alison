/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, ko, Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/subtotal'
            },
            getPureValue: function() {
                var totals = quote.getTotals()();
                if (totals) {
                    return totals.subtotal;
                }
                return quote.subtotal;
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            },

            onRenderComplete: function() {
                var initInput = function() {
                    $('.opc-wrapper').find(".input-text").each(function() {
                        $(this).parent().parent().addClass("brk-form-wrap");
                    });
                }

                var count = 0;
                setTimeout( function(e) {
                   /* 
                    var count1 = $('.opc-wrapper').find(".input-text").length;
                    if (count != count1) {
                        count = count1;
                        setTimeout(initInput, 500);
                    } else {
                        return;
                    }*/
                    initInput();
                }, 500);
            },

        });
    }
);

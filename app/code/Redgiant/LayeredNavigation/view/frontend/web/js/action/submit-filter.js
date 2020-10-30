/**
 */

define(
    [
        'jquery',
        'mage/storage',
        'Redgiant_LayeredNavigation/js/model/loader',
        'mage/apply/main'
    ],
    function ($, storage, loader, mage) {
        'use strict';

        var productContainer = $('#layer-product-list'),
            layerContainer = $('.layered-filter-block-container');

        return function (submitUrl, isChangeUrl) {
            /** save active state */
            var actives = [];
            $('.filter-options-item').each(function (index) {
                if ($(this).hasClass('active')) {
                    actives.push($(this).attr('attribute'));
                }
            });
            window.layerActiveTabs = actives;

            /** start loader */
            loader.startLoader();

            /** change browser url */
            if (typeof window.history.pushState === 'function' && (typeof isChangeUrl === 'undefined')) {
                window.history.pushState({url: submitUrl}, '', submitUrl);
            }

            return storage.post(submitUrl, {}).done(
                function (response) {
                    if (response.backUrl) {
                        window.location = response.backUrl;
                        return;
                    }
                    if (response.navigation) {
                        layerContainer.html(response.navigation);
                    }
                    if (response.products) {
                        productContainer.html(response.products);
                    }

                    if (mage) {
                        mage.apply();
                    }
                }
            ).fail(
                function () {
                    window.location.reload();
                }
            ).always(
                function () {
                    loader.stopLoader();
                }
            );
        };
    }
);

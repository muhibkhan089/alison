define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/error-processor',
        'mage/translate',
        'Magento_Ui/js/modal/alert',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        $,
        Component,
        quote,
        customer,
        errorProcessor,
        $t,
        alert,
        modal,
        fullScreenLoader
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magenest_Moneris/payment/moneris-redirect-form',
                redirectAfterPlaceOrder: false,
                active: false,
                scriptLoaded: false,
                imports: {
                    onActiveChange: 'active'
                }
            },

            initObservable: function () {
                this._super()
                    .observe('active scriptLoaded');
                return this;
            },

            isActive: function () {
                var active = this.getCode() === this.isChecked();
                this.active(active);

                return active;
            },

            onActiveChange: function (isActive) {
                if (isActive && !this.scriptLoaded()) {
                    this.loadScript();
                }
            },

            loadScript: function () {
                var state = this.scriptLoaded;
                state(true);
            },

            getCode: function () {
                return 'moneris';
            },

            getHppId : function () {
                return window.checkoutConfig.payment[this.getCode()].hppid;
            },
            getHppKey : function () {
                return window.checkoutConfig.payment[this.getCode()].hppkey;
            },
            getPaymentUrl : function () {
                return window.checkoutConfig.payment[this.getCode()].paymentUrl;
            },
            isUsCountry: function () {
                return window.checkoutConfig.payment[this.getCode()].isUSCountry;
            },

            getOrderDataUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].getOrderData;
            },

            getCancelOrderUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].orderCancelUrl;
            },

            isValid: function () {
                return window.checkoutConfig.payment[this.getCode()].isValid;
            },

            beforePlaceOrder: function () {
                var self = this;
                if (self.isValid()) {
                    self.placeOrder();
                } else {
                    alert({
                        title: 'ERROR',
                        content: 'Something wrong happened! We can\'t process the checkout! Please contact with the admin for more help!',
                        clickableOverlay: true,
                        actions: {
                            always: function (){}
                        }
                    });
                }
            },

            afterPlaceOrder: function () {
                var self = this;
                $.ajax({
                    url : self.getOrderDataUrl(),
                    data : {
                        'isUs' : self.isUsCountry(),
                        'form_key' : window.checkoutConfig.formKey
                    },
                    type : 'POST'
                }).done(
                        function (response) {
                            var params;
                            if (self.isUsCountry()) {
                                params = { 'hpp_id' : self.getHppId(),'hpp_key':self.getHppKey()};
                                params = Object.assign(params, response);
                            } else {
                                params = { 'ps_store_id' : self.getHppId(),'hpp_key':self.getHppKey()};
                                params = Object.assign(params, response);
                            }
                            var form = document.createElement("form");
                            form.setAttribute("method", "post");
                            form.setAttribute("action", self.getPaymentUrl());
                            for (var key in params) {
                                if (params.hasOwnProperty(key)) {
                                    var hiddenField = document.createElement("input");
                                    hiddenField.setAttribute("type", "hidden");
                                    hiddenField.setAttribute("name", key);
                                    hiddenField.setAttribute("value", params[key]);

                                    form.appendChild(hiddenField);
                                }
                            }

                            document.body.appendChild(form);
                            form.submit();
                        }.bind(this)
                    ).fail(
                        function (response) {
                            var resultCallback = function () {
                                fullScreenLoader.stopLoader();
                                errorProcessor.process(response, self.messageContainer);
                            };
                            $.post(self.getCancelOrderUrl())
                                .done(resultCallback)
                                .fail(resultCallback);
                        }
                );
            }
        });
    }
);

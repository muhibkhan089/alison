define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/model/payment/additional-validators',
        'ko',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/view/billing-address'
    ],
    function ($, ccFormComponent, additionalValidators, ko, alert, address) {
        'use strict';

        return ccFormComponent.extend({
            defaults: {
                template: 'Magenest_Moneris/payment/moneris-direct-form',
                active: false,
                scriptLoaded: false,
                saveKeyData: false,
                imports: {
                    onActiveChange: 'active'
                }
            },
            placeOrderHandler: null,
            validateHandler: null,

            initObservable: function () {
                this._super()
                    .observe('active');
                this._super()
                    .observe('saveKeyData');
                return this;
            },

            disableArrowKeys: function () {
                if ( event.which === 38 || event.which === 40 ) {
                    event.preventDefault();
                } else {
                    return true;
                }
            },

            validateInput: function (e) {
                var ele = $(e);
                if (ele.val() !== "") {
                    ele.valid('isValid');
                }
            },

            context: function () {
                return this;
            },

            getConfig: function (key) {
                if (window.checkoutConfig.payment[this.getCode()][key] !== undefined) {
                    return window.checkoutConfig.payment[this.getCode()][key];
                }
                return null;
            },

            hasVerification: function () {
                if (this.getConfig('cvd') == 1) {
                    return true;
                }
                return false;
            },

            /**
             * @returns {Boolean}
             */
            isShowLegend: function () {
                return true;
            },

            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },

            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },


            getCode: function () {
                return 'moneris';
            },

            getKeyDataUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].getKeyData;
            },

            isVisible: function () {
                if (window.checkoutConfig.payment[this.getCode()].isVaultEnabled == 1) {
                    return window.checkoutConfig.payment[this.getCode()].isLoggedIn && 1;
                }
                return false;
            },

            isProvided: function () {
                if (this.creditCardNumber() && this.creditCardExpMonth() && this.creditCardExpYear() && this.selectedCardType()) {
                    return true
                }
                return false
            },

            isUsCountry: function () {
                return window.checkoutConfig.payment[this.getCode()].isUSCountry;
            },

            isActive: function () {
                var active = this.getCode() == this.isChecked();

                this.active(active);

                return active;
            },

            saveKey: function () {
                var self = this;
                $.ajax({
                    url : self.getKeyDataUrl(),
                    data : {
                        'isUs' : self.isUsCountry(),
                        'form_key' : window.checkoutConfig.formKey,
                        'card_data' : this.getData(),
                        'address': {
                            'street': address().currentBillingAddress()['street'],
                            'post_code': address().currentBillingAddress()['postcode']
                        }
                    },
                    type : 'POST',
                    showLoader : true
                }).done(
                    function (response) {
                        this.placeOrder();
                        return response;
                    }.bind(this)
                ).fail(
                    function (response) {
                        return false;
                    }
                );
            },

            placeOrder: function () {
                if (additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    this._super();
                }
            },

            creditCardValidate: function () {
                var type = this.selectedCardType();
                var availableTypesValues = this.getCcAvailableTypesValues();
                var valid = false;
                for (var i = 0; i < availableTypesValues.length; i++) {
                    if (type == availableTypesValues[i]["value"]) {
                        valid = true;
                        break;
                    }
                }
                if (!valid) {
                    this.selectedCardType(null);
                }
            },

            checkOption: function () {
                if ($('#co-transparent-form').valid('isValid')) {
                    if (this.isProvided()) {
                        if (this.saveKeyData()) {
                            if (this.saveKey()) {
                                return true;
                            }
                        } else {
                            this.placeOrder();
                        }
                    }
                    else {
                        alert({
                            title: 'ERROR',
                            content: 'Please provide credit card information first!',
                            clickableOverlay: true,
                            actions: {
                                always: function (){}
                            }
                        });
                    }
                }
            }
        });
    }
);

/**
 */

var config = {
    paths: {
        touchPunch: 'Redgiant_Core/js/jquery.ui.touch-punch.min',
        rgDevbridgeAutocomplete: 'Redgiant_Core/js/jquery.autocomplete.min'
    },
    shim: {
        rgDevbridgeAutocomplete: ["jquery"],
        touchPunch: ['jquery', 'jquery/ui']
    }
};

require(['jquery', 'mage/translate'], function ($) {
    $(document).on('click', '.delete-action', function (e) {
        e.preventDefault();
        $(this).closest('.data-row').remove();
    }).on('click', '.add-action', function (e) {
        e.preventDefault();
        $(this).closest('tr').before('<tr class="data-row">' +
            '<td colspan="3">' +
            '<div class="admin__field-control control">' +
            '<input type="text"' +
            ' class="input-text admin__control-text" ' +
            ' placeholder="' +
            $.mage.__('Enter Product ID') +
            '" name="products[]" form="edit_form"/>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="data-grid-cell-content">' +
            '<a class="delete-action" href="#">' +
            $.mage.__('Delete') +
            '</a>' +
            '</div>' +
            '</td>' +
            '</tr>');
    });
});
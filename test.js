var field = document.getElementById('hikashop_product_quantity_field_1');
if (hikashopCheckChangeForm('order', 'hikashop_checkout_form')) {
    if (hikashopCheckMethods()) {
        document.getElementById('hikashop_validate').value = 1;
        this.disabled = true;
        document.forms['hikashop_checkout_form'].submit();
    }
}
return false;
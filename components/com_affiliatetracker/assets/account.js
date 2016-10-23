// JavaScript Document

var newAccount = false;

jQuery( document ).ready(function() {

    jQuery("#registerAccountBtn").click(function() {
        newAccount = true;
        jQuery("#formNewAffiliateAccount").removeClass('hidden');
        jQuery(".newJoomlaAccountField").removeClass('hidden');
        jQuery("#notLoggedUserSection").addClass('hidden');
    });

    jQuery("#submitAccountBtn").click(function(e) {
        if (newAccount) {
            validateNewAccountFields(e);
        }
    });

});

/**
 * Checks if the new user fields on the new affiliate account are correct
 * @param e. Submit event
 */
function validateNewAccountFields(e) {
    var valid = true;

    var acc_password = jQuery('#account_password');
    var acc_username = jQuery('#account_username');
    var acc_email = jQuery('#account_email');
    var acc_password_confirm = jQuery('#account_password_confirm');

    if (acc_password.val() === '') {
        jQuery('#acc_password_group').addClass('error');
        jQuery('#account_password_error').removeClass('hidden');
        acc_password.focus();
        valid = false;
    } else {
        jQuery('#acc_password_group').removeClass('error');
        jQuery('#account_password_error').addClass('hidden');
    }

    if (acc_username.val() === '') {
        jQuery('#acc_username_group').addClass('error');
        jQuery('#account_username_error').removeClass('hidden');
        acc_username.focus();
        valid = false;
    } else {
        jQuery('#acc_username_group').removeClass('error');
        jQuery('#account_username_error').addClass('hidden');
    }

    if (acc_email.val() === '') {
        jQuery('#acc_email_group').addClass('error');
        jQuery('#account_email_error').removeClass('hidden');
        acc_email.focus();
        valid = false;
    } else {
        jQuery('#acc_email_group').removeClass('error');
        jQuery('#account_email_error').addClass('hidden');
    }

    if (acc_password.val() !== acc_password_confirm.val()) {
        jQuery('#acc_repeat_password_group').addClass('error');
        jQuery('#account_password_confirm_error').removeClass('hidden');
        acc_password_confirm.focus();
        valid = false;
    } else {
        jQuery('#acc_repeat_password_group').removeClass('error');
        jQuery('#account_password_confirm_error').addClass('hidden');
    }

    if (!valid) e.preventDefault();
}
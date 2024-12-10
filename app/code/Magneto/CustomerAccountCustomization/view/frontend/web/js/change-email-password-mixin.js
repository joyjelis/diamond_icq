define([
    'jquery',
    'mage/translate'
    ], function($,$t) {
        'use strict';

        return function(widget) {
            $.widget('mage.changeEmailPassword', widget, {
                _hideAll: function () {
                    $(this.options.mainContainerSelector).hide();
                    //$(this.options.emailContainerSelector).hide();
                    $(this.options.newPasswordContainerSelector).hide();
                    $(this.options.confirmPasswordContainerSelector).hide();

                    $(this.options.currentPasswordSelector).removeAttr('data-validate').prop('disabled', true);
                    //$(this.options.emailSelector).removeAttr('data-validate').prop('disabled', true);
                    $(this.options.newPasswordSelector).removeAttr('data-validate').prop('disabled', true);
                    $(this.options.confirmPasswordSelector).removeAttr('data-validate').prop('disabled', true);
                },
                _showAll: function () {
                    //$(this.options.titleSelector).html(this.options.titleChangeEmailAndPassword);

                    $(this.options.mainContainerSelector).show();
                    $(this.options.emailContainerSelector).show();
                    $(this.options.newPasswordContainerSelector).show();
                    $(this.options.confirmPasswordContainerSelector).show();

                    $(this.options.currentPasswordSelector).attr('data-validate', '{required:true}').prop('disabled', false);
                    $(this.options.emailSelector).attr('data-validate', '{required:true}').prop('disabled', false);
                    this._updatePasswordFieldWithEmailValue();
                    $(this.options.confirmPasswordSelector).attr(
                        'data-validate',
                        '{required:true, equalTo:"' + this.options.newPasswordSelector + '"}'
                    ).prop('disabled', false);
                },
                _showEmail: function () {
                   this._showAll();
                    //$(this.options.titleSelector).html(this.options.titleChangeEmail);
                    $(this.options.newPasswordContainerSelector).hide();
                    $(this.options.confirmPasswordContainerSelector).hide();
                    $(this.options.currentPasswordSelector).removeAttr('data-validate').prop('disabled', false);
                    $(this.options.newPasswordSelector).removeAttr('data-validate').prop('disabled', true);
                    $(this.options.confirmPasswordSelector).removeAttr('data-validate').prop('disabled', true);
                },
                _showPassword: function () {
                    this._showAll();
                    //$(this.options.titleSelector).html(this.options.titleChangePassword);
                    //$(this.options.emailContainerSelector).hide();
                    $(this.options.emailSelector).removeAttr('data-validate').prop('disabled', true);
                }

                
        });
        return $.mage.changeEmailPassword;
        }
    });
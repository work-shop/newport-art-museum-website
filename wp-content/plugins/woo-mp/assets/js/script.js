jQuery(function($) {

    wooMP.$main        = $('#woo-mp #woo-mp-main');
    wooMP.$cardNum     = $('#woo-mp #cc-num');
    wooMP.$cardExp     = $('#woo-mp #cc-exp');
    wooMP.$cardCVC     = $('#woo-mp #cc-cvc');

    $chargeNotice      = $('#woo-mp #charge-notice');
    $chargeAmount      = $('#woo-mp #charge-amount');
    $chargeBtn         = $('#woo-mp #charge-btn');

    var noticeTemplate = _.template($('#notice-template').html());

    init();

    function init() {
        $('.nav-tab').click(showTab);
        $(document).on('click', '[data-toggle="collapse"]', toggleCollapse);

        wooMP.$cardNum.payment('formatCardNumber');
        wooMP.$cardExp.payment('formatCardExpiry');
        wooMP.$cardCVC.payment('formatCardCVC');

        initChargeAmountAutocomplete();
        $chargeAmount.on('input', updateButton);
        updateButton();

        $('#charge').keypress(chargeEnter);
        $chargeBtn.click(submit);

        $( '#woo-mp-rating-request a' ).click(rated);
    }

    function showTab() {
        $('.tab-content').removeClass('tab-content-active');
        $('.nav-tab').removeClass('nav-tab-active');

        $($(this).attr('href')).addClass('tab-content-active');
        $(this).addClass('nav-tab-active');
        
        return false;
    }

    function notice(selector, type, message, details) {
        var $elem = $(selector);

        var html = noticeTemplate({
            id:      $elem[0].id,
            type:    type,
            message: message,
            details: details
        });

        $elem.html(html).slideDown('fast');
    }

    function toggleCollapse() {
        var $this = $(this);

        $('#' + $this.attr('aria-controls')).slideToggle('fast');
        $this.attr('aria-expanded', $this.attr('aria-expanded') === 'true' ? 'false' : 'true');
    }

    function initChargeAmountAutocomplete() {
        var suggestions             = [];
        var shouldUpdateSuggestions = true;
        var autocompleteMenuIsOpen  = false;

        $chargeAmount.on('keydown', function (event) {
            if (! autocompleteMenuIsOpen) {
                if (_.contains([$.ui.keyCode.UP, $.ui.keyCode.DOWN], event.keyCode)) {
                    $chargeAmount.autocomplete('enable');
                }
            }
        });

        $chargeAmount.autocomplete({
            disabled:  true,
            minLength: 0,
            position:  { my: 'left top+2' },
            source:    function (request, respond) {
                if (shouldUpdateSuggestions) {
                    $.get(wooMP.AJAXURL, {action: 'woo_mp_get_charge_amount_suggestions', order_id: $('#post_ID').val()})
                        .done(function (response) {
                            if (! $.isPlainObject(response) || $.isEmptyObject(response)) {
                                return;
                            }

                            suggestions = [];

                            $.each(response, function (label, value) {
                                suggestions.push({
                                    label: label + ':<span class="alignright">' + formatMoney(value) + '</span>',
                                    value: value
                                });
                            });

                            shouldUpdateSuggestions = false;

                            respond(suggestions);
                        });
                } else {
                    respond(suggestions);
                }
            },
            create:    function () {
                $('.charge-amount-container .ui-autocomplete').attr('tabindex', '-1');
            },
            open:      function () {
                if (! autocompleteMenuIsOpen) {
                    $('.charge-amount-container .ui-autocomplete').hide().slideDown('fast');
                }

                autocompleteMenuIsOpen = true;
            },
            close:     function () {
                $('.charge-amount-container .ui-autocomplete').show().delay(100).slideUp('fast');

                autocompleteMenuIsOpen = false;

                $chargeAmount.autocomplete('disable');
            },
            select:    function (event, ui) {
                $chargeAmount.val(ui.item.value);
                updateButton();
            }
        });

        $chargeAmount.autocomplete('instance')._resizeMenu = function () {
            this.menu.element.outerWidth('auto');
        };

        $chargeAmount.autocomplete('instance')._renderItem = function (ul, item) {
            return $('<li>').append(item.label).appendTo(ul);
        };

        $chargeAmount.focus(function () {
            shouldUpdateSuggestions = true;
        });

        $('#charge-amount-suggestions-btn').click(function () {
            $chargeAmount.focus();
            $chargeAmount.autocomplete('enable');
            $chargeAmount.autocomplete('search', '');
        });

        $chargeAmount.on('keydown', updateButton);
    }

    function updateButton() {
        var total = accounting.unformat($chargeAmount.val(), woocommerce_admin.mon_decimal_point);

		$chargeBtn.text('Charge ' + formatMoney(total));
    }

    function chargeEnter(event) {
        if (event.keyCode == 13) {
            event.preventDefault();

            if (! $chargeBtn.prop('disabled')) {
                submit();
            }
        }
    }

    function submit() {
        $('#woo-mp #charge input').removeClass('invalid');

        if (! valid()) return;

        wooMP.blockUI();
        $chargeBtn.prop('disabled', true);

        wooMP.beginProcessing();
    }

    function valid() {
        if (! wooMP.$cardNum.val()) {
            wooMP.handleError('Please enter a card number.', 'cc-num');
            return false;
        }

        if (! $.payment.validateCardNumber(wooMP.$cardNum.val())) {
            wooMP.handleError('Sorry, the card number is not valid.', 'cc-num');
            return false;
        }

        if (! wooMP.$cardExp.val()) {
            wooMP.handleError('Please enter an expiration date.', 'cc-exp');
            return false;
        }

        if (! $.payment.validateCardExpiry(
            wooMP.$cardExp.payment('cardExpiryVal').month,
            wooMP.$cardExp.payment('cardExpiryVal').year
        )) {
            wooMP.handleError('Sorry, the expiration date is not valid.', 'cc-exp');
            return false;
        }

        if (
            wooMP.$cardCVC.val() &&
            ! $.payment.validateCardCVC(wooMP.$cardCVC.val(), $.payment.cardType(wooMP.$cardNum.val()))
        ) {
            wooMP.handleError('Sorry, the security code is not valid.', 'cc-cvc');
            return false;
        }

        if ($chargeAmount[0].validity.badInput) {
            wooMP.handleError('Please enter a number (e.g. 1.99) without any other symbols.', 'charge-amount');
            return false;
        }

        if ($chargeAmount[0].validity.rangeUnderflow) {
            wooMP.handleError('Please enter a positive number in the amount field.', 'charge-amount');
            return false;
        }

        if (! $chargeAmount.val() || $chargeAmount.val() === "0") {
            wooMP.handleError('Please enter an amount to charge.', 'charge-amount');
            return false;
        }

        // Allow for payment processors to implement their own validation.
        if (wooMP.valid) {
            if (! wooMP.valid()) return false;
        }

        return true;
    }

    wooMP.processPayment = function (paymentData, successCallback) {
        var data = {
            action:   'woo_mp_charge',
            order_id: $('#post_ID').val(),
            amount:   wooMP.formatMoneyForProcessing($chargeAmount.val()),
            currency: wooMP.currency
        };

        // Combine the default data with the payment data from the individual payment processor.
        $.extend(data, paymentData);

        successCallback = successCallback || doSuccess;

        $.post(wooMP.AJAXURL, data)
            .done(function (response) {
                if (response.status == 'success') {
                    successCallback(response);
                } else {
                    if (response.message) {
                        wooMP.catchError(response.message, response.data);
                    } else {
                        wooMP.handleError('Sorry, there was an unknown error. Please check your PHP log file.');
                    }
                }
            })
            .fail(function () {
                wooMP.handleError('Sorry, there was an unknown error. Please check your PHP log file.');
            })
    }

    wooMP.blockUI = function () {
        wooMP.$main.block({
            message:    null,
            overlayCSS: {
                background: "#fff",
                opacity:    .6
            }
        });
    }

    wooMP.handleError = function (message, field, details) {
        notice($chargeNotice, 'error', message, details);

        if (field) {
            $('#' + field).addClass('invalid').focus();
        }

        $chargeBtn.prop('disabled', false);

        wooMP.$main.unblock();
    }

    function formatMoney(amount) {
        return accounting.formatMoney(amount, {
			symbol:    wooMP.currencySymbol,
			decimal:   woocommerce_admin_meta_boxes.currency_format_decimal_sep,
			thousand:  woocommerce_admin_meta_boxes.currency_format_thousand_sep,
			precision: 2,
			format:    woocommerce_admin_meta_boxes.currency_format
		})
    }

    wooMP.formatMoneyForProcessing = function (amount) {
        return accounting.formatMoney(amount, {
			decimal:   '.',
			thousand:  '',
			precision: 2,
			format:    '%v'
        });
    }

    function doSuccess() {

        // If we're on the 'Add new order' page, then location.reload() would create a new order.
        // Using location.href also has the added benefit of scrolling the page to the top, where
        // the user can see the success notice and the order note.
        location.href = 'post.php?post=' + $('#post_ID').val() + '&action=edit';
    }

    function rated() {
        $.post( wooMP.AJAXURL, { action: 'woo_mp_rated' } );
        $('span#woo-mp-rating-request').text('Thank you ☺');
    }

});
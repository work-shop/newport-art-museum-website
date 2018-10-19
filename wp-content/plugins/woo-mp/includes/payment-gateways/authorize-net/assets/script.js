jQuery(function($) {

    $taxAmount     = $('#woo-mp #tax-amount');
    $freightAmount = $('#woo-mp #freight-amount');
    $dutyAmount    = $('#woo-mp #duty-amount');

    wooMP.valid = function () {
        if ($taxAmount[0].validity.badInput) {
            wooMP.handleError('Please enter a number (e.g. 1.99) without any other symbols.', 'tax-amount');
            return false;
        }

        if ($freightAmount[0].validity.badInput) {
            wooMP.handleError('Please enter a number (e.g. 1.99) without any other symbols.', 'freight-amount');
            return false;
        }

        if ($dutyAmount[0].validity.badInput) {
            wooMP.handleError('Please enter a number (e.g. 1.99) without any other symbols.', 'duty-amount');
            return false;
        }

        if ($taxAmount[0].validity.rangeUnderflow) {
            wooMP.handleError('Please enter a positive number in the tax amount field.', 'tax-amount');
            return false;
        }

        if ($freightAmount[0].validity.rangeUnderflow) {
            wooMP.handleError('Please enter a positive number in the freight amount field.', 'freight-amount');
            return false;
        }

        if ($dutyAmount[0].validity.rangeUnderflow) {
            wooMP.handleError('Please enter a positive number in the duty amount field.', 'duty-amount');
            return false;
        }

        if ($('#po-number').val().length > 25) {
            wooMP.handleError('The PO number field has a limit of 25 characters.', 'po-number');
            return false;
        }

        return true;
    }

    wooMP.beginProcessing = function () {
        var authData = {
            apiLoginID: wooMP.loginID,
            clientKey:  wooMP.clientKey
        };

        var cardData = {
            cardNumber: wooMP.$cardNum.val().replace(/\s/g, ''),
            month:      wooMP.$cardExp.val().split(' / ')[0],
            year:       (wooMP.$cardExp.val().split(' / ')[1] || '').slice(-2),
            cardCode:   wooMP.$cardCVC.val()
        };
    
        Accept.dispatchData({
            authData: authData,
            cardData: cardData
        }, 'authorizeResponseHandler');
    }

    window.authorizeResponseHandler = function (response) {
        if (response.messages.resultCode == 'Error') {
            wooMP.$main.unblock();

            wooMP.catchError(response.messages.message[0].text);
        } else {
            wooMP.processPayment({
                token:          response.opaqueData.dataValue,
                tax_amount:     wooMP.formatMoneyForProcessing($taxAmount.val()),
                freight_amount: wooMP.formatMoneyForProcessing($freightAmount.val()),
                duty_amount:    wooMP.formatMoneyForProcessing($dutyAmount.val()),
                po_number:      $('#po-number').val(),
                tax_exempt:     $('#tax-exempt').prop('checked')
            });
        }
    }

    wooMP.catchError = function (message, code, data) {
        switch (message) {

            // Authorize.Net returns this error when an expiration date is very far in the future.
            case 'The credit card has expired.':
                wooMP.handleError('Sorry, the expiration date is not valid.', 'cc-exp');
                break;
            case 'An error occurred during processing. Please try again.':
                wooMP.handleError('Sorry, there was an error. The most likely reason is that the Login ID is not valid. Please check your settings and try again.');
                break;
            case 'User authentication failed due to invalid authentication values.':
                wooMP.handleError('Sorry, the Login ID, or Client Key, or both, are not valid. Please check your settings and try again.');
                break;
            default:
                var detailsArray = ((data || {}).response || {}).additional_response_code_details || [];
                var detailsHTML  = null;

                if (detailsArray.length) {
                    detailsHTML = '<ul>';

                    detailsArray.forEach(function (note) {
                        detailsHTML += '<li>' + note + '</li>';
                    });

                    detailsHTML += '</ul>';
                }

                wooMP.handleError(message, null, detailsHTML);
                break;
        }
    }

});
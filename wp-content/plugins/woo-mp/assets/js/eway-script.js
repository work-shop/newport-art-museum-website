jQuery(function($) {

    $cardName = $('#card-name');

    wooMP.valid = function () {
        if (! wooMP.$cardCVC.val()) {
            wooMP.handleError("Please enter the card security code.", 'cc-cvc');
            return false;
        }

        if (! $cardName.val()) {
            wooMP.handleError("Please enter the cardholder name.", 'card-name');
            return false;
        }

        return true;
    }

    wooMP.beginProcessing = function () {
        wooMP.processPayment({
            sub_action:   'get_access_code',
            redirect_url: location.href
        }, function (response) {
            submitToEway(response.data.form_action_url, response.data.access_code);
        });
    }
    
    function submitToEway(formActionURL, accessCode) {
        var form = $(_.template($('#eway-form-template').html())({
            formActionURL:   formActionURL,
            accessCode:      accessCode,
            cardName:        $cardName.val(),
            cardNumber:      wooMP.$cardNum.val().replace(/\s/g, ''),
            cardExpiryMonth: wooMP.$cardExp.val().split(' / ')[0],
            cardExpiryYear:  (wooMP.$cardExp.val().split(' / ')[1] || '').slice(-2),
            cardCVN:         wooMP.$cardCVC.val()
        }))[0];

        eWAY.process(form, {
            autoRedirect: false,
            onComplete:   function (data) { checkStatus(accessCode, data) },
            onError:      function () { checkStatus(accessCode) },
            onTimeout:    function () { checkStatus(accessCode) }
        });
    }

    function checkStatus(accessCode, data) {
        if (data && data.Errors) {
            wooMP.catchError(data.Errors.split(',')[0]);
            return;
        }

        wooMP.processPayment({
            sub_action:  'get_transaction_status',
            access_code: accessCode,
            last_4:      wooMP.$cardNum.val().slice(-4)
        });
    }

    wooMP.catchError = function (error, data) {
        var details = null;

        if ((data && data.error_code) || /^[A-Z]\d{4}$/.test(error)) {
            var errorCode = (data && data.error_code) || error;
            var searchURL = 'https://go.eway.io/s/search/All/Home/' + errorCode;
            details       = '<p>Search eWAY for response code: <a href="' + searchURL + '" target="_blank"><em>' +
                            errorCode + '</em></a></p>';
        }

        wooMP.handleError(wooMP.responseCodeMessages[error] || error, null, details);
    }

});
jQuery(function($) {

    wooMP.beginProcessing = function () {
        try {
            Stripe.setPublishableKey(wooMP.publishableKey);
            Stripe.card.createToken({
                number: wooMP.$cardNum.val(),
                exp:    wooMP.$cardExp.val(),
                cvc:    wooMP.$cardCVC.val()
            }, stripeResponseHandler);
        } catch (error) {
            wooMP.handleError(error.message);
        }
    }

    function stripeResponseHandler(status, response) {
        if (response.error) {
            wooMP.$main.unblock();

            wooMP.catchError(response.error.message);
        } else {
            wooMP.processPayment({
                token: response.id
            });
        }
    }

    wooMP.catchError = function (message) {
        switch (message) {

            // Stripe returns this error when an expiration date is very far in the future.
            case "Your card's expiration year is invalid.":
                wooMP.handleError('Sorry, the expiration date is not valid.', 'cc-exp');
                break;
            case "Your card's security code is incorrect.":
                wooMP.handleError('Sorry, the security code is not valid.', 'cc-cvc');
                break;
            default:
                wooMP.handleError(message);
                break;
        }
    }

});
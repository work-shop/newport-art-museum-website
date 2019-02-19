jQuery(document).ready(function ($) {

    $(document).on("click", "a.remove-amount", function (e) {
        e.preventDefault();

        var data = {
            'action': 'remove_gift_card_amount',
            'amount': $(this).closest("span.variation-amount").find('input[name="gift-card-amounts[]"]').val(),
            'product_id': $("#post_ID").val()
        };

        var clicked_item = $(this).closest("span.variation-amount");
        clicked_item.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywgc_vars.loader + ") no-repeat center",
                opacity: .6
            }
        });

        $.post(ywgc_vars.ajax_url, data, function (response) {
            if (1 == response.code) {
                clicked_item.remove();
            }

            clicked_item.unblock();
        });

    });

    /**
     * Add a new amount to current gift card
     * @param item
     */
    function add_amount(item) {
        var data = {
            'action': 'add_gift_card_amount',
            'amount': $("#gift_card-amount").val(),
            'product_id': $("#post_ID").val()
        };

        var clicked_item = item.closest("span.add-new-amount-section");
        clicked_item.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywgc_vars.loader + ") no-repeat center",
                opacity: .6
            }
        });

        $.post(ywgc_vars.ajax_url, data, function (response) {
            if (1 == response.code) {
                $("p._gift_card_amount_field").replaceWith(response.value);
            }

            clicked_item.unblock();
        });
    }

    /**
     * Add a new amount for the current gift card
     */
    $(document).on("click", "a.add-new-amount", function (e) {
        e.preventDefault();
        add_amount($(this));
    });

    /**
     * Add a new amount for the current gift card
     */
    $(document).on('keypress', 'input#gift_card-amount', function (e) {
        if (event.which === 13) {
            e.preventDefault();

            //Disable textbox to prevent multiple submit
            $(this).attr("disabled", "disabled");

            //Do Stuff, submit, etc..
            add_amount($(this));

            $(this).removeAttr("disabled");

        }
    });
});
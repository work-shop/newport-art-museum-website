jQuery(document).ready(function ($) {

    show_hide_add_to_cart_button();

    function show_hide_add_to_cart_button() {
        var current_selection = $(".gift-cards-list select");

        if (!(current_selection.val())) {
            $("div.single_variation_wrap").css('display', 'none');
        }
        else {
            $("div.single_variation_wrap").css('display', '');
        }
    }

    $(document).on('change', '.gift-cards-list select', function (e) {
        show_hide_add_to_cart_button();

    })
});
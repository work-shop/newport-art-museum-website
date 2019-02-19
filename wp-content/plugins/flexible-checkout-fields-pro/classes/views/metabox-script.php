<script type="text/javascript">
	jQuery(document).ready(function() {
	    var select_fields = jQuery('#checkout_fields_fields_editor select');
		if (jQuery.fn.selectWoo) {
			select_fields.selectWoo();
		} else {
			if (jQuery.fn.select2) {
				select_fields.select2();
			}
		}
	})
</script>
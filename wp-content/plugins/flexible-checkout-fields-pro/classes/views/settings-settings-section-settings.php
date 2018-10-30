<?php
	$section_types = array(
			'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'
	);
?>
<div id="post-body" class="fields-container">
	<h3><?php _e( 'Section Settings', 'flexible-checkout-fields-pro' ); ?></h3>
    <div>
       	<label for="section_title"><?php _e( 'Section Title', 'flexible-checkout-fields-pro' ); ?></label>
		<input type="text" id="section_title" name="inspire_checkout_fields[section_settings][<?php echo $section; ?>][section_title]" value="<?php if( !empty($section_settings[$section]['section_title'])): echo $section_settings[$section]['section_title']; else: echo '' ; endif;  ?>" />
    </div>

    <div>
       	<label for="section_title_type"><?php _e( 'Section Title Tag', 'flexible-checkout-fields-pro' ); ?></label>
		<select type="text" id="section_title_type" name="inspire_checkout_fields[section_settings][<?php echo $section; ?>][section_title_type]">
			<?php foreach ( $section_types as $section_type ) : ?>
				<?php $selected = ''; ?>
				<?php if ( isset($section_settings[$section]['section_title_type'] ) && $section_settings[$section]['section_title_type'] == $section_type ) $selected = ' selected'; ?>
				<option value="<?php echo $section_type; ?>" <?php echo $selected; ?>><?php echo $section_type; ?></option>
			<?php endforeach; ?>
		</select>
    </div>

    <div>
       	<label for="section_css"><?php _e( 'CSS Class', 'flexible-checkout-fields-pro' ); ?></label>
		<input type="text" id="section_css" name="inspire_checkout_fields[section_settings][<?php echo $section; ?>][section_css]" value="<?php if( !empty($section_settings[$section]['section_css'])): echo $section_settings[$section]['section_css']; else: echo '' ; endif;  ?>">
    </div>
</div>

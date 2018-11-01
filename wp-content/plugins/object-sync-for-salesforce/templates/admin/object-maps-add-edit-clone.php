<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="object_map">
	<input type="hidden" name="redirect_url_error" value="<?php echo esc_url( $error_url ); ?>" />
	<input type="hidden" name="redirect_url_success" value="<?php echo esc_url( $success_url ); ?>" />
	<?php if ( isset( $transient ) ) { ?>
	<input type="hidden" name="transient" value="<?php echo esc_html( $transient ); ?>" />
	<?php } ?>
	<input type="hidden" name="action" value="post_object_map" >
	<input type="hidden" name="method" value="<?php echo esc_attr( $method ); ?>" />
	<?php if ( 'edit' === $method ) { ?>
	<input type="hidden" name="id" value="<?php echo absint( $map['id'] ); ?>" />
	<?php } ?>
	<div class="object_map_wordpress_id">
		<label for="wordpress_id"><?php echo esc_html__( 'WordPress ID', 'object-sync-for-salesforce' ); ?>: </label>
		<input type="text" id="wordpress_id" name="wordpress_id" required value="<?php echo isset( $wordpress_id ) ? esc_html( $wordpress_id ) : ''; ?>" />
	</div>
	<div class="object_map_salesforce_id">
		<label for="salesforce_id"><?php echo esc_html__( 'Salesforce ID', 'object-sync-for-salesforce' ); ?>: </label>
		<input type="text" id="salesforce_id" name="salesforce_id" required value="<?php echo isset( $salesforce_id ) ? esc_html( $salesforce_id ) : ''; ?>" />
	</div>
	<fieldset class="wordpress_side">
		<div class="wordpress_object">
			<label for="wordpress_object"><?php echo esc_html__( 'WordPress Object', 'object-sync-for-salesforce' ); ?>: </label>
			<select id="wordpress_object" name="wordpress_object" required>
				<option value="">- <?php echo esc_html__( 'Select object type', 'object-sync-for-salesforce' ); ?> -</option>
				<?php
				$wordpress_objects = $this->wordpress->wordpress_objects;
				foreach ( $wordpress_objects as $object ) {
					if ( isset( $wordpress_object ) && $wordpress_object === $object ) {
						$selected = ' selected';
					} else {
						$selected = '';
					}
					echo sprintf( '<option value="%1$s"%2$s>%3$s</option>',
						esc_html( $object ),
						esc_attr( $selected ),
						esc_html( $object )
					);
				}
				?>
			</select>
		</div>
	</fieldset>
	<?php
		submit_button(
			// translators: the placeholder refers to the currently selected method (add, edit, or clone)
			sprintf( esc_html__( '%1$s object_map', 'object-sync-for-salesforce' ), ucfirst( $method ) )
		);
	?>
</form>

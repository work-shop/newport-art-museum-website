<?php
extract( $args );
?>

<div class="wrap wpdesk">
	<?php /* screen_icon(); */ ?>
    <h1><?php _e( 'WP Desk', 'wpdesk-helper' ); ?><a
                href="<?php echo admin_url( 'admin.php?page=wpdesk-helper&refresh=1' ); ?>"
                class="page-title-action"><?php echo __( 'Refresh', 'wpdesk-helper' ); ?></a></h1>
	<?php if ( ! is_array( $wpdesk_plugins ) ) : ?>
        <p>
			<?php echo __( 'Error: ', 'wpdesk-helper' ); ?><?php echo $wpdesk_plugins; ?>
        </p>
        <p>
			<?php echo __( 'To diagnose problem we suggest run this commands from command line on your server:', 'wpdesk-helper' ); ?>
            <br/>
            <textarea cols="60">ping www.wpdesk.pl</textarea><br/>
            <br/>
            <textarea cols="60">ping www.wpdesk.net</textarea><br/>
            <br/>
            <textarea cols="60">traceroute www.wpdesk.pl</textarea><br/>
            <br/>
            <textarea cols="60">traceroute www.wpdesk.net</textarea><br/>
            <br/>
            <textarea cols="60">wget &quot;<?php echo $url ?>&quot;</textarea><br/>
            <br/>
            <textarea cols="60">curl &quot;<?php echo $url ?>&quot;</textarea><br/>
        </p>
	<?php else : ?>
		<?php echo wpautop( $wpdesk_plugins['header'] ); ?>
        <div class="wp-list-table widefat plugin-install">
            <div id="the-list">
				<?php
				foreach ( $wpdesk_plugins['plugins'] as $plugin ) {
					$plugin_state = 'not present';
					foreach ( $wp_plugins as $wp_key => $wp_plugin ) {
						if ( $wp_plugin['PluginURI'] == $plugin['url'] ) {
							$plugin_state = 'not active';
							if ( is_plugin_active( $wp_key ) ) {
								$plugin_state = 'active';
								if ( ! empty( $wpdesk_installed_plugins[ $wp_key ] ) && is_object( $wpdesk_installed_plugins[ $wp_key ] ) && get_option( $wpdesk_installed_plugins[ $wp_key ]->activated_key, '0' ) != 'Activated' ) {
									$plugin_state = 'not active key';
								} else {
									$plugin_state = 'active key';
								}
							}
							break;
						}
					}

					$plugin_slug = sanitize_title( $plugin['name'] );
					$utm_link    = '?utm_source=wpdesk-helper&utm_medium=plugin-card&utm_campaign=wpdesk-helper-page&utm_content=' . $plugin_slug;
					?>
                    <div class="plugin-card">
                        <div class="plugin-card-top">
                            <div class="column-name">
                                <h3>
                                    <a href="<?php echo $plugin['url'] . $utm_link; ?>">
										<?php echo $plugin['name']; ?>
                                    </a>
                                </h3>
                            </div>
                            <div class="">
                                <a href="<?php echo $plugin['url'] . $utm_link; ?>" target="_blank"><img
                                            src="<?php echo str_replace( 'http://', '//', $plugin['image'] ); ?>"></a>
								<?php echo $plugin['description']; ?>
                            </div>
                        </div>
                        <div class="plugin-card-bottom">
                            <div class="vers column-rating">
								<?php if ( $plugin_state == 'not present' ) : ?>
                                    <a class="button button-primary" href="<?php echo $plugin['url'] . $utm_link; ?>"
                                       target="_blank">
										<?php echo __( 'Download plugin', 'wpdesk-helper' ); ?>
                                    </a>
								<?php endif; ?>
								<?php if ( $plugin_state == 'not active' ) : ?>
                                    <a class="button"
                                       href="<?php echo admin_url( 'plugins.php#' . sanitize_title( $wp_plugin['Name'] ) ); ?>">
										<?php echo __( 'Activate plugin', 'wpdesk-helper' ); ?>
                                    </a>
								<?php endif; ?>
								<?php if ( $plugin_state == 'not active key' ) : ?>
                                    <a class="button"
                                       href="<?php echo admin_url( 'admin.php?page=wpdesk-licenses' ); ?>">
										<?php echo __( 'Activate key', 'wpdesk-helper' ); ?>
                                    </a>
								<?php endif; ?>
								<?php if ( $plugin_state == 'active key' && ! empty( $wpdesk_installed_plugins[ $wp_key ] ) && is_object( $wpdesk_installed_plugins[ $wp_key ] ) && $wpdesk_installed_plugins[ $wp_key ]->config_uri && $wpdesk_installed_plugins[ $wp_key ]->config_uri != '' ) : ?>
                                    <a class="button"
                                       href="<?php echo $wpdesk_installed_plugins[ $wp_key ]->config_uri; ?>">
										<?php echo __( 'Configure', 'wpdesk-helper' ); ?>
                                    </a>
								<?php endif; ?>
                            </div>
                            <div class="column-updated">
								<?php if ( $plugin_state == 'active key' && $plugin_state != 'not present' ) : ?>
									<?php if ( $plugin['version'] != $wp_plugin['Version'] ) : ?>
                                        <a class="update-now button"
                                           href="<?php echo admin_url( 'plugins.php#' . sanitize_title( $wp_plugin['Name'] ) ); ?>">
											<?php echo __( 'Update plugin', 'wpdesk-helper' ); ?>
                                        </a>
									<?php endif; ?>
								<?php endif; ?>
                            </div>
                            <div class="column-downloaded"></div>
                            <div class="column-compatibility"></div>
                        </div>
                    </div>
					<?php
				}
				?>
            </div>

            <div style="clear:both;"></div>
        </div>
		<?php echo wpautop( $wpdesk_plugins['footer'] ); ?>
	<?php endif; ?>
</div>

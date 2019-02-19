<?php

namespace WPDesk\Notice;

use WPDesk\PluginBuilder\Plugin\HookablePluginDependant;
use WPDesk\PluginBuilder\Plugin\PluginAccess;

/**
 * Class AjaxHandler
 *
 * AjaxHandler for dismissible notices.
 *
 * @package WPDesk\Notice
 */
class AjaxHandler implements HookablePluginDependant
{

    use PluginAccess;

    const POST_FIELD_NOTICE_NAME = 'notice_name';

    const SCRIPTS_VERSION = '2';
    const SCRIPT_HANDLE = 'wpdesk_notice';

    /**
     * @var string
     */
    private $assetsURL;

    /**
     * AjaxHandler constructor.
     *
     * @param string $assetsURL Assets URL.
     */
    public function __construct($assetsURL)
    {
        $this->assetsURL = $assetsURL;
    }

    /**
     * Hooks.
     */
    public function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        add_action('wp_ajax_wpdesk_notice_dismiss', [$this, 'processAjaxNoticeDismiss']);
    }

    /**
     * Enqueue admin scripts.
     */
    public function enqueueAdminScripts()
    {
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_register_script(
            self::SCRIPT_HANDLE,
            trailingslashit($this->assetsURL) . 'js/notice' . $suffix . '.js',
            array( 'jquery' ),
            self::SCRIPTS_VERSION
        );
        wp_enqueue_script(self::SCRIPT_HANDLE);
    }

    /**
     * Process AJAX notice dismiss.
     *
     * Updates corresponded WordPress option and fires wpdesk_notice_dismissed_notice action with notice name.
     */
    public function processAjaxNoticeDismiss()
    {
        if (isset($_POST[self::POST_FIELD_NOTICE_NAME])) {
            $noticeName = $_POST[self::POST_FIELD_NOTICE_NAME];
            update_option(
                PermanentDismissibleNotice::OPTION_NAME_PREFIX . $noticeName,
                PermanentDismissibleNotice::OPTION_VALUE_DISMISSED
            );
            do_action('wpdesk_notice_dismissed_notice', $noticeName);
        }
        if (defined('DOING_AJAX') && DOING_AJAX) {
            die();
        }
    }

}


<?php

use \WPDesk\Notice\AjaxHandler;
use \WPDesk\Notice\PermanentDismissibleNotice;

class TestAjaxHandler extends WP_UnitTestCase
{

    const ASSETS_URL = 'http://test.com/test/assetes/';
    const NOTICE_NAME = 'test_notice_name';
    const WP_DEFAULT_PRIORITY = 10;

    public function testHooks()
    {
        $ajaxHandler = new AjaxHandler(self::ASSETS_URL);
        $ajaxHandler->hooks();

        $this->assertEquals(
            self::WP_DEFAULT_PRIORITY,
            has_action('admin_enqueue_scripts', [$ajaxHandler, 'enqueueAdminScripts'])
        );
        $this->assertEquals(
            self::WP_DEFAULT_PRIORITY,
            has_action('wp_ajax_wpdesk_notice_dismiss', [$ajaxHandler, 'processAjaxNoticeDismiss'])
        );
    }

    public function testEnqueueAdminScripts()
    {
        $ajaxHandler = new AjaxHandler(self::ASSETS_URL);
        $ajaxHandler->hooks();
        do_action('admin_enqueue_scripts');
        $registeredScripts = wp_scripts()->registered;

        $this->assertArrayHasKey('wpdesk_notice', $registeredScripts, 'Script not registered!');
        $this->assertEquals(
            self::ASSETS_URL . 'js/notice.js',
            $registeredScripts['wpdesk_notice']->src,
            'Script src is invalid!'
        );
    }

    public function testProcessAjaxNoticeDismiss() {
        $_POST[AjaxHandler::POST_FIELD_NOTICE_NAME] = self::NOTICE_NAME;

        $ajaxHandler = new AjaxHandler(self::ASSETS_URL);
        $ajaxHandler->processAjaxNoticeDismiss();

        $this->assertEquals(
            PermanentDismissibleNotice::OPTION_VALUE_DISMISSED,
            get_option(PermanentDismissibleNotice::OPTION_NAME_PREFIX . self::NOTICE_NAME)
        );
    }

}

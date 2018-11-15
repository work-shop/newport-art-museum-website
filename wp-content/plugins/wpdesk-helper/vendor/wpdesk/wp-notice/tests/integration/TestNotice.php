<?php

use \WPDesk\Notice\Notice;

class TestNotice extends WP_UnitTestCase
{

    public function testAddAction()
    {
        $notice_priority = 11;

        $notice = new Notice(Notice::NOTICE_TYPE_INFO, 'test', false, $notice_priority);

        $this->assertEquals($notice_priority, has_action('admin_notices', [$notice, 'showNotice'], $notice_priority));
    }

    public function testShowNotice()
    {
        $notice = new Notice('test');

        $this->expectOutputString('<div class="notice notice-info"><p>test</p></div>');

        $notice->showNotice();
    }

    public function testShowNoticeError()
    {
        $notice = new Notice('test', Notice::NOTICE_TYPE_ERROR);

        $this->expectOutputString('<div class="notice notice-error"><p>test</p></div>');

        $notice->showNotice();
    }

    public function testShowNoticeWarning()
    {
        $notice = new Notice('test', Notice::NOTICE_TYPE_WARNING);

        $this->expectOutputString('<div class="notice notice-warning"><p>test</p></div>');

        $notice->showNotice();
    }

    public function testShowNoticeSuccess()
    {
        $notice = new Notice('test', Notice::NOTICE_TYPE_SUCCESS);

        $this->expectOutputString('<div class="notice notice-success"><p>test</p></div>');

        $notice->showNotice();
    }

    public function testShowNoticeDismissible()
    {
        $notice = new Notice('test', Notice::NOTICE_TYPE_INFO, true);

        $this->expectOutputString('<div class="notice notice-info is-dismissible"><p>test</p></div>');

        $notice->showNotice();
    }

    public function testNoticeContent()
    {
        $noticeContent = 'test';

        $notice = new Notice($noticeContent);

        $this->assertEquals($noticeContent, $notice->getNoticeContent());

        $noticeContent = 'test 2';
        $notice->setNoticeContent($noticeContent);
        $this->assertEquals($noticeContent, $notice->getNoticeContent());
    }

    public function testNoticeType()
    {
        $notice = new Notice('test', Notice::NOTICE_TYPE_INFO);

        $this->assertEquals(Notice::NOTICE_TYPE_INFO, $notice->getNoticeType());

        $notice->setNoticeType(Notice::NOTICE_TYPE_ERROR);
        $this->assertEquals(Notice::NOTICE_TYPE_ERROR, $notice->getNoticeType());
    }

    public function testDismissible()
    {
        $notice = new Notice('test');

        $this->assertFalse($notice->isDismissible());

        $notice->setDismissible(true);
        $this->assertTrue($notice->isDismissible());
    }

    public function testPriority()
    {
        $notice = new Notice('test');

        $this->assertEquals(10, $notice->getPriority());

        $notice->setPriority(20);
        $this->assertEquals(20, $notice->getPriority());
    }

}

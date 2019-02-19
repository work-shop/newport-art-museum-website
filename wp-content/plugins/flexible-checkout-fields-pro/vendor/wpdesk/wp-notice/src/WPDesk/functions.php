<?php

/**
 * Creates Notice.
 *
 * @param string $noticeContent Notice content.
 * @param string $noticeType Notice type.
 * @param bool   $dismissible Dismissible notice.
 * @param int    $priority Notice priority,
 *
 * @return \WPDesk\Notice\Notice
 */
function WPDeskNotice($noticeContent, $noticeType = 'info', $dismissible = false, $priority = 10)
{
    return \WPDesk\Notice\Factory::notice($noticeContent, $noticeType, $dismissible, $priority);
}

/**
 * Creates Notice.
 *
 * Alias for {@see WPDeskNotice()} function.
 *
 * @param string $noticeContent Notice content.
 * @param string $noticeType Notice type.
 * @param bool   $dismissible Dismissible notice.
 * @param int    $priority Notice priority,
 *
 * @return \WPDesk\Notice\Notice
 */
function wpdesk_notice($noticeContent, $noticeType = 'info', $dismissible = false, $priority = 10)
{
    return WPDeskNotice($noticeContent, $noticeType, $dismissible, $priority);
}

/**
 * Creates Permanent Dismissible Notice.
 *
 * @param string $noticeContent Notice content.
 * @param string $noticeType Notice type.
 * @param int    $priority Notice priority.
 *
 * @return \WPDesk\Notice\Notice
 */
function WPDeskPermanentDismissibleNotice($noticeContent, $noticeType = 'info', $priority = 10)
{
    return \WPDesk\Notice\Factory::permanentDismissibleNotice($noticeContent, $noticeType, $priority);
}

/**
 * Creates Permanent Dismissible Notice.
 *
 * Alias for {@see WPDeskPermanentDismissibleNotice()} function.
 *
 * @param string $noticeContent Notice content.
 * @param string $noticeType Notice type.
 * @param int    $priority Notice priority.
 *
 * @return \WPDesk\Notice\Notice
 */
function wpdesk_permanent_dismissible_notice($noticeContent, $noticeType = 'info', $priority = 10)
{
    return WPDeskPermanentDismissibleNotice($noticeContent, $noticeType, $priority);
}

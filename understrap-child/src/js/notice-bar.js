/**
 * Notice Bar functionality
 * Handles the display and dismissal of the site-wide notice bar
 */
import CookieHandler from './utils/CookieHandler';

// Constants
const NOTICE_SETTINGS = {
    COOKIE_NAME: 'wordpress_notice_dismissed',
    COOKIE_EXPIRY_DAYS: 365,
    ANIMATION_DURATION: 300,
    SELECTORS: {
        noticeBar: '#notice-bar',
        dismissButton: '.notice-dismiss'
    }
};

/**
 * Handle notice bar animation and removal
 * @param {HTMLElement} noticeBar - The notice bar element
 */
const hideNoticeBar = (noticeBar) => {
    Object.assign(noticeBar.style, {
        transition: 'opacity 0.3s ease, max-height 0.3s ease',
        opacity: '0',
        maxHeight: '0',
        overflow: 'hidden'
    });

    setTimeout(() => noticeBar.remove(), NOTICE_SETTINGS.ANIMATION_DURATION);
};

/**
 * Initialize notice bar functionality
 */
const initNoticeBar = () => {
    const noticeBar = document.querySelector(NOTICE_SETTINGS.SELECTORS.noticeBar);
    const dismissButton = document.querySelector(NOTICE_SETTINGS.SELECTORS.dismissButton);

    if (noticeBar && dismissButton) {
        dismissButton.addEventListener('click', () => {
            CookieHandler.set(NOTICE_SETTINGS.COOKIE_NAME, 'true', {
                expiryDays: NOTICE_SETTINGS.COOKIE_EXPIRY_DAYS
            });

            hideNoticeBar(noticeBar);
        });
    }
};

document.addEventListener('DOMContentLoaded', initNoticeBar);
/**
 * Simple Cookie Handler Class
 */
class CookieHandler {
    /**
     * Set a cookie with the given name, value, and expiry days
     * @param {string} name - Cookie name
     * @param {string} value - Cookie value
     * @param {number} [days] - Number of days until cookie expires
     */
    static set(name, value, days = null) {
        let cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}; path=/`;
        
        if (days) {
            const date = new Date();
            date.setDate(date.getDate() + days);
            cookie += `; expires=${date.toUTCString()}`;
        }

        document.cookie = cookie;
    }

    /**
     * Get a cookie value by name
     * @param {string} name - Cookie name
     * @returns {string|null} Cookie value or null if not found
     */
    static get(name) {
        const value = document.cookie.match('(^|;)\\s*' + encodeURIComponent(name) + '=([^;]+)');
        return value ? decodeURIComponent(value[2]) : null;
    }

    /**
     * Remove a cookie by name
     * @param {string} name - Cookie name
     */
    static remove(name) {
        this.set(name, '', -1);
    }
}

export default CookieHandler;
<?php
/**
 * EnPharChem - CSRF protection
 * ----------------------------
 * One token per session, verified for every POST at a single choke point in
 * index.php. Enforcing centrally rather than per handler means a new POST
 * action cannot forget to check it — the default is protected.
 *
 * Browsers submit the token in the `_csrf` hidden field (Csrf::field()).
 * JSON / fetch callers send it in the `X-CSRF-Token` header, reading it from
 * the csrf-token <meta> tag rendered by views/layouts/main.php.
 */

class Csrf
{
    const SESSION_KEY = '_csrf_token';
    const FIELD       = '_csrf';
    const HEADER      = 'HTTP_X_CSRF_TOKEN';

    /** The session's token, minted on first use. */
    public static function token()
    {
        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /** Drop the token so the next token() mints a fresh one (called on login). */
    public static function rotate()
    {
        unset($_SESSION[self::SESSION_KEY]);
    }

    /** Hidden input to drop inside any POST form. */
    public static function field()
    {
        return '<input type="hidden" name="' . self::FIELD . '" value="'
             . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    /** Just the token value, for the <meta> tag and JS callers. */
    public static function metaTag()
    {
        return '<meta name="csrf-token" content="'
             . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    /** The token the client sent, from the form field or the AJAX header. */
    private static function submitted()
    {
        if (isset($_POST[self::FIELD]) && is_string($_POST[self::FIELD])) {
            return $_POST[self::FIELD];
        }
        if (isset($_SERVER[self::HEADER]) && is_string($_SERVER[self::HEADER])) {
            return $_SERVER[self::HEADER];
        }
        return '';
    }

    /**
     * Constant-time comparison of the submitted token against the session's.
     * Reads the session value directly rather than via token(), so a missing
     * token fails the check instead of silently minting a new one.
     */
    public static function check()
    {
        $expected = $_SESSION[self::SESSION_KEY] ?? '';
        $given    = self::submitted();
        if (!is_string($expected) || $expected === '' || $given === '') {
            return false;
        }
        return hash_equals($expected, $given);
    }

    /** Does this client expect a JSON reply rather than an HTML error page? */
    public static function clientWantsJson()
    {
        return stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false
            || stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false
            || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}

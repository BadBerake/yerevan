<?php

class Lang {
    private static $currentLang = 'en';
    private static $translations = [];

    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_GET['lang'])) {
            $lang = $_GET['lang'];
            if (in_array($lang, ['en', 'hy', 'ru', 'fa', 'ar'])) {
                $_SESSION['lang'] = $lang;
            }
        }

        self::$currentLang = $_SESSION['lang'] ?? 'en';
        self::loadTranslations();
    }

    public static function current() {
        return self::$currentLang;
    }

    public static function getDir() {
        return in_array(self::$currentLang, ['fa', 'ar']) ? 'rtl' : 'ltr';
    }

    private static function loadTranslations() {
        $file = __DIR__ . '/lang/' . self::$currentLang . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            self::$translations = require __DIR__ . '/lang/en.php';
        }
    }

    public static function get($key) {
        return self::$translations[$key] ?? $key;
    }

    public static function t($translations, $default = '') {
        if (empty($translations)) return $default;
        
        $data = is_string($translations) ? json_decode($translations, true) : $translations;
        
        if (!is_array($data)) return $default;
        
        // Try current language
        if (!empty($data[self::$currentLang])) {
            return $data[self::$currentLang];
        }
        
        // Fallback to English if current is not English
        if (self::$currentLang !== 'en' && !empty($data['en'])) {
            return $data['en'];
        }
        
        return $default;
    }
}

function __($key) {
    return Lang::get($key);
}

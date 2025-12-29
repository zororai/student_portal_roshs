<?php

namespace App\Helpers;

use App\WebsiteSetting;

class WebsiteHelper
{
    /**
     * Get a website setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function setting($key, $default = null)
    {
        return WebsiteSetting::get($key, $default);
    }

    /**
     * Get the site logo URL
     *
     * @return string
     */
    public static function logo()
    {
        $logo = WebsiteSetting::get('site_logo', 'images/logo.png');
        return asset($logo);
    }

    /**
     * Get the favicon URL
     *
     * @return string
     */
    public static function favicon()
    {
        $favicon = WebsiteSetting::get('favicon', 'images/favicon.ico');
        return asset($favicon);
    }

    /**
     * Get the site name
     *
     * @return string
     */
    public static function siteName()
    {
        return WebsiteSetting::get('site_name', 'Rose Of Sharon High School');
    }

    /**
     * Get the site tagline
     *
     * @return string
     */
    public static function tagline()
    {
        return WebsiteSetting::get('site_tagline', 'Foundation');
    }

    /**
     * Get theme colors as CSS variables
     *
     * @return string
     */
    public static function themeStyles()
    {
        $primary = WebsiteSetting::get('primary_color', '#2d5016');
        $secondary = WebsiteSetting::get('secondary_color', '#1a365d');
        $accent = WebsiteSetting::get('accent_color', '#d69e2e');
        $headerBg = WebsiteSetting::get('header_bg_color', '#ffffff');
        $footerBg = WebsiteSetting::get('footer_bg_color', '#1a202c');

        return "
        <style>
            :root {
                --primary-color: {$primary};
                --secondary-color: {$secondary};
                --accent-color: {$accent};
                --header-bg-color: {$headerBg};
                --footer-bg-color: {$footerBg};
            }
            .text-primary { color: var(--primary-color) !important; }
            .bg-primary { background-color: var(--primary-color) !important; }
            .text-secondary { color: var(--secondary-color) !important; }
            .bg-secondary { background-color: var(--secondary-color) !important; }
            .text-accent { color: var(--accent-color) !important; }
            .bg-accent { background-color: var(--accent-color) !important; }
            .btn-primary, .button-theme { background-color: var(--primary-color) !important; }
            .btn-primary:hover, .button-theme:hover { background-color: var(--secondary-color) !important; }
        </style>
        ";
    }

    /**
     * Get all settings as an array for easy access
     *
     * @return array
     */
    public static function all()
    {
        $settings = WebsiteSetting::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        return $result;
    }
}

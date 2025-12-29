<?php

use App\WebsiteSetting;

if (!function_exists('website_setting')) {
    /**
     * Get a website setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function website_setting($key, $default = null)
    {
        return WebsiteSetting::get($key, $default);
    }
}

if (!function_exists('site_logo')) {
    /**
     * Get the site logo URL
     *
     * @return string
     */
    function site_logo()
    {
        $logo = WebsiteSetting::get('site_logo', 'images/logo.png');
        return asset($logo);
    }
}

if (!function_exists('site_name')) {
    /**
     * Get the site name
     *
     * @return string
     */
    function site_name()
    {
        return WebsiteSetting::get('site_name', 'Rose Of Sharon High School');
    }
}

if (!function_exists('theme_styles')) {
    /**
     * Get theme colors as CSS variables
     *
     * @return string
     */
    function theme_styles()
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
        </style>
        ";
    }
}

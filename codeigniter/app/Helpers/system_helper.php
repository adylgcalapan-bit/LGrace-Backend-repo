<?php

if (! function_exists('format_system_date')) {
    function format_system_date(?string $date, bool $withTime = false): string
    {
        if (empty($date)) {
            return 'N/A';
        }

        $selectedFormat = 'MM/DD/YYYY';

        try {
            $db = \Config\Database::connect();

            static $cachedFormat = null;

            if ($cachedFormat === null) {
                $settings = $db->table('settings')
                    ->select('date_format')
                    ->orderBy('setting_id', 'ASC')
                    ->get()
                    ->getRowArray();

                $cachedFormat = $settings['date_format'] ?? 'MM/DD/YYYY';
            }

            $selectedFormat = $cachedFormat;
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Unable to load date format setting: ' . $e->getMessage()
            );
        }

        $phpFormats = [
            'MM/DD/YYYY' => 'm/d/Y',
            'DD/MM/YYYY' => 'd/m/Y',
            'YYYY/MM/DD' => 'Y/m/d',
        ];

        $phpFormat = $phpFormats[$selectedFormat] ?? 'm/d/Y';

        if ($withTime) {
            $phpFormat .= ' - h:i A';
        }

        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return 'N/A';
        }

        return date($phpFormat, $timestamp);
    }
    if (! function_exists('system_theme_class')) {
        function system_theme_class(): string
        {
            $selectedTheme = 'Light';

            try {
                $db = \Config\Database::connect();

                static $cachedTheme = null;

                if ($cachedTheme === null) {
                    $settings = $db->table('settings')
                        ->select('theme_preference')
                        ->orderBy('setting_id', 'ASC')
                        ->get()
                        ->getRowArray();

                    $cachedTheme = $settings['theme_preference'] ?? 'Light';
                }

                $selectedTheme = $cachedTheme;
            } catch (\Throwable $e) {
                log_message(
                    'error',
                    'Unable to load theme preference: ' . $e->getMessage()
                );
            }

            return $selectedTheme === 'Dark'
                ? 'theme-dark'
                : 'theme-light';
        }
    }
}

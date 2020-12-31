<?php

function formatDate($date): string
{
    return \Carbon\Carbon::parse($date)->format('d.m.Y');
}

function formatTime($date): string
{
    return \Carbon\Carbon::parse($date)->format('H:i');
}

function purify($html): string
{
    return \Mews\Purifier\Facades\Purifier::clean($html, function (HTMLPurifier_Config $config) {
    });
}

function viewMobileOrDesktop($view_name, $data = [], $merge_data = [])
{
    $agent = new \Jenssegers\Agent\Agent;
    return view(($agent->isMobile() ? 'mobile.' : '') . $view_name, $data, $merge_data);
}

const extensionToIconMap = [
    'xls' => 'excel',
    'xlsx' => 'excel',
    'docx' => 'word',
];

function docIconByFilename(string $filename): string
{
    $icon = extensionToIconMap[pathinfo($filename)['extension'] ?? ''] ?? 'undefined';
    return url("/images/icons/$icon.png");
}

const FORMAT_SIZE_PRECISION = 1;
const FORMAT_SIZE_UNITS = ['B', 'KB', 'MB', 'GB', 'TB'];

function format_size(int $bytes): string
{
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, 1) . ' ' . FORMAT_SIZE_UNITS[$i];
}

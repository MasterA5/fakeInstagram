<?php

function getUserTheme($conn, $user_id) {
    $stmt = $conn->prepare("SELECT theme, palette FROM users WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return [
        'theme' => $user['theme'] ?? 'dark',
        'palette' => $user['palette'] ?? null,
    ];
}

function getBaseTheme($theme) {
    $themes = [
        'dark' => [
            '--bg-primary' => '#000000',
            '--bg-secondary' => '#09090b',
            '--bg-card' => '#18181b',
            '--bg-card-hover' => '#27272a',
            '--border' => '#27272a',
            '--text-primary' => '#ffffff',
            '--text-secondary' => '#a1a1aa',
            '--accent' => '#6366f1',
            '--accent-hover' => '#818cf8',
            '--danger' => '#ef4444',
            '--success' => '#22c55e',
        ],
        'light' => [
            '--bg-primary' => '#fafafa',
            '--bg-secondary' => '#ffffff',
            '--bg-card' => '#ffffff',
            '--bg-card-hover' => '#f4f4f5',
            '--border' => '#e4e4e7',
            '--text-primary' => '#18181b',
            '--text-secondary' => '#71717a',
            '--accent' => '#6366f1',
            '--accent-hover' => '#4f46e5',
            '--danger' => '#dc2626',
            '--success' => '#16a34a',
        ],
        'ocean' => [
            '--bg-primary' => '#0f172a',
            '--bg-secondary' => '#1e293b',
            '--bg-card' => '#1e293b',
            '--bg-card-hover' => '#334155',
            '--border' => '#334155',
            '--text-primary' => '#f1f5f9',
            '--text-secondary' => '#94a3b8',
            '--accent' => '#06b6d4',
            '--accent-hover' => '#22d3ee',
            '--danger' => '#ef4444',
            '--success' => '#22c55e',
        ],
        'sunset' => [
            '--bg-primary' => '#1c1917',
            '--bg-secondary' => '#292524',
            '--bg-card' => '#292524',
            '--bg-card-hover' => '#44403c',
            '--border' => '#44403c',
            '--text-primary' => '#fefce8',
            '--text-secondary' => '#a8a29e',
            '--accent' => '#f97316',
            '--accent-hover' => '#fb923c',
            '--danger' => '#ef4444',
            '--success' => '#22c55e',
        ],
        'forest' => [
            '--bg-primary' => '#0a0f0a',
            '--bg-secondary' => '#0f1a0f',
            '--bg-card' => '#1a2a1a',
            '--bg-card-hover' => '#2a3a2a',
            '--border' => '#2a3a2a',
            '--text-primary' => '#f0fdf0',
            '--text-secondary' => '#9ca89c',
            '--accent' => '#22c55e',
            '--accent-hover' => '#4ade80',
            '--danger' => '#ef4444',
            '--success' => '#22c55e',
        ],
        'rose' => [
            '--bg-primary' => '#1a0a0f',
            '--bg-secondary' => '#2a0f1a',
            '--bg-card' => '#2a1a20',
            '--bg-card-hover' => '#3a2a30',
            '--border' => '#3a2a30',
            '--text-primary' => '#fdf0f5',
            '--text-secondary' => '#b0a0a8',
            '--accent' => '#ec4899',
            '--accent-hover' => '#f472b6',
            '--danger' => '#ef4444',
            '--success' => '#22c55e',
        ],
        'midnight' => [
            '--bg-primary' => '#050a14',
            '--bg-secondary' => '#0a1428',
            '--bg-card' => '#14203a',
            '--bg-card-hover' => '#1e3050',
            '--border' => '#1e3050',
            '--text-primary' => '#f0f4ff',
            '--text-secondary' => '#8a9ab8',
            '--accent' => '#3b82f6',
            '--accent-hover' => '#60a5fa',
            '--danger' => '#ef4444',
            '--success' => '#22c55e',
        ],
        'amber' => [
            '--bg-primary' => '#141008',
            '--bg-secondary' => '#1e1a0a',
            '--bg-card' => '#2a2414',
            '--bg-card-hover' => '#3a3420',
            '--border' => '#3a3420',
            '--text-primary' => '#fefce8',
            '--text-secondary' => '#b0a880',
            '--accent' => '#f59e0b',
            '--accent-hover' => '#fbbf24',
            '--danger' => '#ef4444',
            '--success' => '#22c55e',
        ],
    ];

    if (!isset($themes[$theme])) {
        $theme = 'dark';
    }

    return $themes[$theme];
}

function applyTheme($themeName, $paletteJson = null) {
    $vars = getBaseTheme($themeName);

    if ($paletteJson) {
        $palette = json_decode($paletteJson, true);
        if (is_array($palette)) {
            foreach ($palette as $key => $value) {
                if (isset($vars[$key])) {
                    $vars[$key] = $value;
                }
            }
            // Recalculate accent-hover if accent changed
            if (isset($palette['--accent']) && !isset($palette['--accent-hover'])) {
                $vars['--accent-hover'] = lightenColor($palette['--accent'], 20);
            }
        }
    }

    $css = ':root {';
    foreach ($vars as $key => $value) {
        $css .= "$key: $value;";
    }
    $css .= '}';
    return $css;
}

function lightenColor($hex, $percent) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6) return $hex;
    $r = min(255, hexdec(substr($hex, 0, 2)) + $percent);
    $g = min(255, hexdec(substr($hex, 2, 2)) + $percent);
    $b = min(255, hexdec(substr($hex, 4, 2)) + $percent);
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

function getUserPalette($conn, $user_id) {
    $stmt = $conn->prepare("SELECT palette FROM users WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['palette'] ?? null;
}

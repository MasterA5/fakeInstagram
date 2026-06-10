<?php

function getUserTheme($conn, $user_id) {
    $stmt = $conn->prepare("SELECT theme FROM users WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['theme'] ?? 'dark';
}

function applyTheme($theme) {
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
    ];

    if (!isset($themes[$theme])) {
        $theme = 'dark';
    }

    $vars = $themes[$theme];
    $css = ':root {';
    foreach ($vars as $key => $value) {
        $css .= "$key: $value;";
    }
    $css .= '}';
    return $css;
}

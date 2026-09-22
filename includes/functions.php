<?php
// includes/functions.php
// Global helper functions for UPPHL PHP Application

if (!function_exists('upphl_contact_settings')) {
    function upphl_contact_settings() {
        static $settings = null;
        if ($settings !== null) return $settings;

        $default = [
            'phone'     => '+91 7084900009',
            'email'     => 'uphandballleague@gmail.com',
            'address'   => 'D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006',
            'mapEmbed'  => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8577.971649410943!2d82.97167766165312!3d25.317881457351188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x398e2df211e6302d%3A0x71a3709ed12c0baa!2sChandpur%2C%20Chandua%20Chhittupur%2C%20Shivpurwa%2C%20Varanasi%2C%20Uttar%20Pradesh%20221002!5e0!3m2!1sen!2sin!4v1787729110796!5m2!1sen!2sin',
            'facebook'  => 'https://facebook.com/upprohandballleague',
            'instagram' => 'https://instagram.com/upprohandballleague',
            'youtube'   => 'https://www.youtube.com/@sportscastindia'
        ];

        $jsonFile = __DIR__ . '/../uploads/contact_settings.json';
        if (file_exists($jsonFile)) {
            $custom = json_decode(file_get_contents($jsonFile), true);
            if (is_array($custom)) {
                $default = array_merge($default, $custom);
            }
        }

        $settings = $default;
        return $settings;
    }
}

if (!function_exists('upphl_page_banner')) {
    function upphl_page_banner($pageKey) {
        static $pageBanners = null;
        if ($pageBanners === null) {
            $jsonFile = __DIR__ . '/../uploads/page_banners.json';
            if (file_exists($jsonFile)) {
                $pageBanners = json_decode(file_get_contents($jsonFile), true) ?? [];
            } else {
                $pageBanners = [];
            }
        }
        return $pageBanners[$pageKey] ?? null;
    }
}

if (!function_exists('is_active_page')) {
    function is_active_page($targetPage) {
        $curr = basename($_SERVER['PHP_SELF'] ?? '');
        if ($curr === $targetPage) return ' active';
        if ($curr === '' && $targetPage === 'index.php') return ' active';
        return '';
    }
}

if (!function_exists('upphl_get_teams')) {
    function upphl_get_teams($season = null, $activeOnly = true) {
        static $teamsCache = null;
        if ($teamsCache === null) {
            $jsonFile = __DIR__ . '/../uploads/teams.json';
            if (file_exists($jsonFile)) {
                $teamsCache = json_decode(file_get_contents($jsonFile), true) ?? [];
            } else {
                $teamsCache = [];
            }
        }

        $list = $teamsCache;

        // Sort by sortOrder ASC
        usort($list, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['name'] ?? '', $b['name'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $list = array_filter($list, function($t) {
                return ($t['status'] ?? 'Active') === 'Active';
            });
        }

        if (!empty($season) && strtolower($season) !== 'all') {
            $list = array_filter($list, function($t) use ($season) {
                return strtolower(trim($t['season'] ?? '')) === strtolower(trim($season));
            });
        }

        return array_values($list);
    }
}

if (!function_exists('upphl_get_team_seasons')) {
    function upphl_get_team_seasons() {
        $all = upphl_get_teams(null, false);
        $seasons = [];
        foreach ($all as $t) {
            $s = trim($t['season'] ?? '');
            if (!empty($s) && !in_array($s, $seasons)) {
                $seasons[] = $s;
            }
        }
        rsort($seasons);
        return $seasons;
    }
}

if (!function_exists('upphl_get_news')) {
    function upphl_get_news($limit = null, $activeOnly = true) {
        static $newsCache = null;
        if ($newsCache === null) {
            $jsonFile = __DIR__ . '/../uploads/news.json';
            if (file_exists($jsonFile)) {
                $newsCache = json_decode(file_get_contents($jsonFile), true) ?? [];
            } else {
                $newsCache = [];
            }
        }

        $list = $newsCache;

        // Sort by sortOrder ASC
        usort($list, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($b['id'] ?? '', $a['id'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $list = array_filter($list, function($n) {
                return ($n['status'] ?? 'Active') === 'Active';
            });
        }

        $list = array_values($list);

        if ($limit !== null && $limit > 0) {
            $list = array_slice($list, 0, $limit);
        }

        return $list;
    }
}

if (!function_exists('upphl_get_partners')) {
    function upphl_get_partners($activeOnly = true) {
        static $partnersCache = null;
        if ($partnersCache === null) {
            $jsonFile = __DIR__ . '/../uploads/partners.json';
            if (file_exists($jsonFile)) {
                $partnersCache = json_decode(file_get_contents($jsonFile), true) ?? [];
            } else {
                $partnersCache = [];
            }
        }

        $list = $partnersCache;

        // Sort by sortOrder ASC
        usort($list, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['id'] ?? '', $b['id'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $list = array_filter($list, function($p) {
                return ($p['status'] ?? 'Active') === 'Active';
            });
        }

        return array_values($list);
    }
}

if (!function_exists('upphl_get_committee')) {
    function upphl_get_committee($activeOnly = true) {
        static $committeeCache = null;
        if ($committeeCache === null) {
            $jsonFile = __DIR__ . '/../uploads/committee.json';
            if (file_exists($jsonFile)) {
                $committeeCache = json_decode(file_get_contents($jsonFile), true) ?? [];
            } else {
                $committeeCache = [];
            }
        }

        $list = $committeeCache;

        // Sort by sortOrder ASC
        usort($list, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['id'] ?? '', $b['id'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $list = array_filter($list, function($m) {
                return ($m['status'] ?? 'Active') === 'Active';
            });
        }

        return array_values($list);
    }
}

if (!function_exists('upphl_get_live_partners')) {
    function upphl_get_live_partners($activeOnly = true) {
        static $partnersCache = null;
        if ($partnersCache === null) {
            $jsonFile = __DIR__ . '/../uploads/live_partners.json';
            if (!file_exists($jsonFile)) {
                require_once __DIR__ . '/../api/controllers/LivePartnerController.php';
                $lpc = new LivePartnerController(null);
                $res = $lpc->getAll(false, true);
                $partnersCache = $res['partners'] ?? [];
            } else {
                $partnersCache = json_decode(file_get_contents($jsonFile), true) ?? [];
            }
        }

        $list = $partnersCache;

        // Sort by sortOrder ASC
        usort($list, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['id'] ?? '', $b['id'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $list = array_filter($list, function($p) {
                return ($p['status'] ?? 'Active') === 'Active';
            });
        }

        return array_values($list);
    }
}

if (!function_exists('upphl_get_live_videos')) {
    function upphl_get_live_videos($season = null, $activeOnly = true) {
        static $videosCache = null;
        if ($videosCache === null) {
            $jsonFile = __DIR__ . '/../uploads/live_videos.json';
            if (!file_exists($jsonFile)) {
                require_once __DIR__ . '/../api/controllers/LiveVideoController.php';
                $lvc = new LiveVideoController(null);
                $res = $lvc->getAll('', '', false, true);
                $videosCache = $res['videos'] ?? [];
            } else {
                $videosCache = json_decode(file_get_contents($jsonFile), true) ?? [];
            }
        }

        $list = $videosCache;

        // Sort by sortOrder ASC
        usort($list, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($b['id'] ?? '', $a['id'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $list = array_filter($list, function($v) {
                return ($v['status'] ?? 'Active') === 'Active';
            });
        }

        if (!empty($season) && strtolower($season) !== 'all') {
            $list = array_filter($list, function($v) use ($season) {
                return strtolower(trim($v['season'] ?? '')) === strtolower(trim($season));
            });
        }

        return array_values($list);
    }
}

if (!function_exists('upphl_get_live_video_seasons')) {
    function upphl_get_live_video_seasons() {
        $all = upphl_get_live_videos(null, false);
        $seasons = [];
        foreach ($all as $v) {
            $s = trim($v['season'] ?? '');
            if (!empty($s) && !in_array($s, $seasons)) {
                $seasons[] = $s;
            }
        }
        rsort($seasons);
        return $seasons;
    }
}if (!function_exists('upphl_get_announcements')) {
    function upphl_get_announcements($activeOnly = true) {
        static $announcementsCache = null;
        if ($announcementsCache === null) {
            $jsonFile = __DIR__ . '/../uploads/announcements.json';
            if (file_exists($jsonFile)) {
                $announcementsCache = json_decode(file_get_contents($jsonFile), true) ?? [];
            } else {
                $announcementsCache = [];
            }
        }

        $list = $announcementsCache;

        // Sort by sortOrder ASC
        usort($list, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $list = array_filter($list, function($item) {
                return ($item['status'] ?? 'Active') === 'Active';
            });
        }

        return array_values($list);
    }
}

if (!function_exists('upphl_get_home_stats')) {
    function upphl_get_home_stats() {
        $default = [
            [
                'id'       => 'stat_1',
                'target'   => '6',
                'suffix'   => '',
                'decimals' => 0,
                'label'    => 'Franchise Teams',
                'icon'     => 'fa-solid fa-shield-halved'
            ],
            [
                'id'       => 'stat_2',
                'target'   => '120',
                'suffix'   => '+',
                'decimals' => 0,
                'label'    => 'Scouted Players',
                'icon'     => 'fa-solid fa-users'
            ],
            [
                'id'       => 'stat_3',
                'target'   => '2',
                'suffix'   => '',
                'decimals' => 0,
                'label'    => 'Successful Seasons',
                'icon'     => 'fa-solid fa-trophy'
            ],
            [
                'id'       => 'stat_4',
                'target'   => '1.5',
                'suffix'   => 'L+',
                'decimals' => 1,
                'label'    => 'Dedicated Fans',
                'icon'     => 'fa-solid fa-heart'
            ]
        ];

        $jsonFile = __DIR__ . '/../uploads/home_stats.json';
        if (file_exists($jsonFile)) {
            $custom = json_decode(file_get_contents($jsonFile), true);
            if (is_array($custom) && !empty($custom)) {
                return $custom;
            }
        }

        return $default;
    }
}

if (!function_exists('upphl_get_standings_seasons')) {
    function upphl_get_standings_seasons() {
        $jsonFile = __DIR__ . '/../uploads/standings.json';
        $standings = file_exists($jsonFile) ? (json_decode(file_get_contents($jsonFile), true) ?? []) : [];
        $seasons = [];
        foreach ($standings as $s) {
            $sn = trim($s['season'] ?? '');
            if (!empty($sn) && !in_array($sn, $seasons)) {
                $seasons[] = $sn;
            }
        }
        rsort($seasons);
        return $seasons;
    }
}

if (!function_exists('upphl_get_standings')) {
    function upphl_get_standings($season = null) {
        $jsonFile = __DIR__ . '/../uploads/standings.json';
        $standings = file_exists($jsonFile) ? (json_decode(file_get_contents($jsonFile), true) ?? []) : [];
        if (empty($standings)) return [];

        if ($season === null) {
            $seasons = upphl_get_standings_seasons();
            $season = !empty($seasons) ? $seasons[0] : null;
        }

        if (!empty($season) && strtolower($season) !== 'all') {
            $standings = array_filter($standings, function($s) use ($season) {
                return strtolower(trim($s['season'] ?? '')) === strtolower(trim($season));
            });
        }

        $list = array_values($standings);
        usort($list, function($a, $b) {
            $ptsA = (int)($a['pts'] ?? 0);
            $ptsB = (int)($b['pts'] ?? 0);
            if ($ptsB !== $ptsA) return $ptsB - $ptsA;
            $gdA = (int)($a['gd'] ?? 0);
            $gdB = (int)($b['gd'] ?? 0);
            if ($gdB !== $gdA) return $gdB - $gdA;
            $gfA = (int)($a['gf'] ?? 0);
            $gfB = (int)($b['gf'] ?? 0);
            return $gfB - $gfA;
        });

        return $list;
    }
}


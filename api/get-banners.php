<?php
// api/get-banners.php (Forwarder to BannerController)
$_GET['scope'] = ($_GET['all'] ?? '') === '1' ? 'all' : 'active';
require_once __DIR__ . '/banners.php';

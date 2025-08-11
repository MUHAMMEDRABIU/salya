<?php
require_once __DIR__ . '/../config/constants.php';

// App/site basics
$appName = defined('APP_NAME') ? APP_NAME : 'Frozen Foods';
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

// Compute base URL that works whether the app is at / or /salya
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri      = $_SERVER['REQUEST_URI'] ?? '/';
$firstSeg = explode('/', trim($uri, '/'))[0] ?? '';
$rootBase = $firstSeg ? '/' . $firstSeg : '';
$baseUrl  = $scheme . '://' . $host . $rootBase;

// Current full URL for canonical
$currentUrl = $scheme . '://' . $host . ($uri ?: '/');

// Prefer explicit constants if available; otherwise fall back to known client paths
$cssBase = defined('CSS_URL') ? rtrim(CSS_URL, '/') . '/' : ($baseUrl . '/client/css/');
$imgBase = defined('IMG_URL') ? rtrim(IMG_URL, '/') . '/' : ($baseUrl . '/client/img/');

// SEO config with sensible defaults
$seo_config = [
    'title'        => isset($page_title) ? $page_title : ($appName . ' | Quality Frozen Foods in Nigeria'),
    'description'  => isset($page_description) ? $page_description : 'Order premium frozen foods online in Nigeria. Fresh chicken, fish, turkey, beef and more delivered nationwide.',
    'keywords'     => isset($page_keywords) ? $page_keywords : 'frozen foods Nigeria, chicken, fish, turkey, beef, delivery, Lagos, Abuja',
    'canonical'    => isset($canonical_url) ? $canonical_url : $currentUrl,
    'og_image'     => isset($og_image) ? $og_image : ($imgBase . 'og-image.jpg'),
    'og_type'      => isset($og_type) ? $og_type : 'website',
    'twitter_card' => isset($twitter_card) ? $twitter_card : 'summary_large_image',
    'robots'       => isset($robots) ? $robots : 'index,follow',
    'locale'       => 'en_NG',
];

// Ensure og_image is absolute URL
if (strpos($seo_config['og_image'], 'http') !== 0) {
    $seo_config['og_image'] = rtrim($baseUrl, '/') . '/' . ltrim($seo_config['og_image'], '/');
}

// Organization schema by default
$structured_data = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => $appName,
    'url'      => $seo_config['canonical'],
    'logo'     => $imgBase . 'logo.png',
    'sameAs'   => [
        'https://www.facebook.com/',
        'https://www.instagram.com/',
        'https://x.com/'
    ]
];

// Optional Product schema when $product is provided
if (isset($product) && is_array($product)) {
    $image = !empty($product['image'])
        ? (strpos($product['image'], 'http') === 0 ? $product['image'] : ($imgBase . ltrim($product['image'], '/')))
        : $seo_config['og_image'];

    $structured_data = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'),
        'description' => htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'),
        'image'       => $image,
        'sku'         => $product['sku'] ?? '',
        'brand'       => [
            '@type' => 'Brand',
            'name'  => $appName
        ],
        'offers'      => [
            '@type'         => 'Offer',
            'price'         => isset($product['price']) ? (string)$product['price'] : '0.00',
            'priceCurrency' => defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'NGN',
            'availability'  => !empty($product['in_stock']) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url'           => $seo_config['canonical']
        ]
    ];
}
?>

<head>
    <!-- Primary Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($seo_config['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seo_config['description'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seo_config['keywords'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($seo_config['robots'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($seo_config['canonical'], ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="<?php echo htmlspecialchars($seo_config['og_type'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($seo_config['title'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seo_config['description'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($seo_config['og_image'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($seo_config['canonical'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:locale" content="<?php echo htmlspecialchars($seo_config['locale'], ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="<?php echo htmlspecialchars($seo_config['twitter_card'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($seo_config['title'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($seo_config['description'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($seo_config['og_image'], ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $imgBase; ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $imgBase; ?>favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $imgBase; ?>apple-touch-icon.png">
    <link rel="shortcut icon" href="<?php echo $imgBase; ?>favicon.png" type="image/x-icon">
    <meta name="theme-color" content="#F97316">

    <!-- Performance: preconnect/dns-prefetch -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    <!-- Vendor JS/CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Project CSS -->
    <link rel="stylesheet" href="<?php echo $cssBase; ?>toasted.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>style.css">

    <!-- Structured Data -->
    <script type="application/ld+json">
        <?php echo json_encode($structured_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    </script>

    <style>
        body {
            font-family: 'DM Sans', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        *:focus-visible {
            outline: 2px solid #F97316;
            outline-offset: 2px;
        }
    </style>
</head>
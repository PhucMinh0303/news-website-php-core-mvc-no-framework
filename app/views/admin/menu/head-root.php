<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo View::escape(SITE_TITLE); ?>" />
    <meta name="keywords" content="Capital AM, Asset Management, Financial Services, Investment" />
    <meta name="author" content="<?php echo View::escape(SITE_NAME); ?>" />
    <meta property="og:title" content="<?php echo View::escape(SITE_TITLE); ?>" />
    <meta property="og:description" content="<?php echo View::escape(SITE_TITLE); ?>" />
    <meta property="og:url" content="<?php echo BASE_URL; ?>" />
    <title><?php echo isset($view_page_title) ? View::escape($view_page_title) . ' - ' . View::escape(SITE_NAME) : View::escape(SITE_TITLE); ?></title>

    <!-- CSS chính -->
    <link rel="stylesheet" href="<?php echo View::asset('css/styles.css'); ?>" />
    <link rel="stylesheet" href="<?php echo View::asset('css/section10.css'); ?>" />
    <link rel="stylesheet" href="<?php echo View::asset('css/responsive-mobile.css'); ?>" />

    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
    />
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />
  </head>

  <body>

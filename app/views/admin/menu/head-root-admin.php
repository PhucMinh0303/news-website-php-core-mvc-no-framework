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

  <!-- Thẻ chứa các thư viện css -->
  

  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

  <!-- CSS chính -->
  <link rel="stylesheet" href="<?php echo View::asset('css/admin/admin.css'); ?>" />
  <link rel="stylesheet" href="<?php echo View::asset('css/admin/news-recruitment_create.css'); ?>" />
  <link rel="stylesheet" href="<?php echo View::asset('css/admin/news-recruitment_admin.css'); ?>" />
  <link rel="stylesheet" href="<?php echo View::asset('css/admin/contact-application_admin.css'); ?>" />

  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
</head>

<body>
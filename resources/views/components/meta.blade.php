<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no viewport-fit=cover, shrink-to-fit=no">

<?php $MAIN_TITLE = 'Computerun 2.0: EXECUTE' ?>

@if (isset($title))
  <title>{{$title}} - {{$MAIN_TITLE}}</title>
  <meta name="title" content="{{$title}} - {{$MAIN_TITLE}}">
  <meta property="og:title" content="{{$title}} - {{$MAIN_TITLE}}">
  <meta property="twitter:title" content="{{$title}} - {{$MAIN_TITLE}}">
@else
  <title>{{$MAIN_TITLE}}</title>
  <meta name="title" content="{{$MAIN_TITLE}}">
  <meta property="og:title" content="{{$MAIN_TITLE}}">
  <meta property="twitter:title" content="{{$MAIN_TITLE}}">
@endif

<!-- Primary Meta Tags -->
<meta name="description" content="Organized by HIMTI and HIMSISFO BINUS UNIVERSITY">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="http://computerun.id/">
<meta property="og:description" content="Organized by HIMTI and HIMSISFO BINUS UNIVERSITY">
<meta property="og:image" content="">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="http://computerun.id/">
<meta property="twitter:description" content="Organized by HIMTI and HIMSISFO BINUS UNIVERSITY">
<meta property="twitter:image" content="">

<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
<link href="/css/index.css" type="text/css" rel="stylesheet"/>
<link href="/fonts/fonts.css" type="text/css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

<!-- Web Manifest, Icons, and PWA -->
<link rel="manifest" href="/manifest.json">

<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="application-name" content="COMPUTERUN">
<meta name="apple-mobile-web-app-title" content="COMPUTERUN">
<meta name="theme-color" content="#0c1631">
<meta name="msapplication-navbutton-color" content="#0c1631">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="msapplication-starturl" content="/">

<link rel="icon" type="image/png" sizes="512x512" href="/img/main-512.png">
<link rel="apple-touch-icon" type="image/png" sizes="512x512" href="/img/main-512.png">
<link rel="icon" type="image/png" sizes="192x192" href="/img/main-192.png">
<link rel="apple-touch-icon" type="image/png" sizes="192x192" href="/img/main-192.png">

<!-- Prevent phone number linking in iOS -->
<meta name="format-detection" content="telephone=no">

<!-- Add support for custom external CSS files -->
@if (isset($custom_css) && is_array($custom_css))
    @foreach ($custom_css as $stylesheet)
        <link href="{{$stylesheet->url}}" type="text/css" rel="stylesheet"
            @if (isset($stylesheet->integrity))
                integrity="{{$stylesheet->integrity}}"
            @endif
            @if (isset($stylesheet->media))
                media="{{$stylesheet->media}}"
            @endif
        >
    @endforeach
@endif

<!-- Add support for custom external JS files -->
@if (isset($custom_js) && is_array($custom_js))
    @foreach ($custom_js as $script)
        <script src="{{$script->url}}"></script>
    @endforeach
@endif

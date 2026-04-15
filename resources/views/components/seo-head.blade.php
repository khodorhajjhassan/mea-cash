@props(['seo' => []])

@php
    $settings = app(App\Services\SettingsService::class);
    $seoService = app(App\Services\SeoService::class);
    
    // Fallback logic if seo array is empty
    if (empty($seo)) {
        $seo = $seoService->forPage('MeaCash');
    }
@endphp

<!-- Basic Meta Tags -->
<title>{{ $seo['title'] }}</title>
@if(!empty($seo['description']))
    <meta name="description" content="{{ $seo['description'] }}">
@endif
@if(!empty($seo['keywords']))
    <meta name="keywords" content="{{ $seo['keywords'] }}">
@endif
@if(!empty($seo['robots']))
    <meta name="robots" content="{{ $seo['robots'] }}">
@endif
@if(!empty($seo['canonical']))
    <link rel="canonical" href="{{ $seo['canonical'] }}">
@endif

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
<meta property="og:url" content="{{ $seo['url'] ?? Request::fullUrl() }}">
<meta property="og:title" content="{{ $seo['title'] }}">
@if(!empty($seo['description']))
    <meta property="og:description" content="{{ $seo['description'] }}">
@endif
@if(!empty($seo['image']))
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif
<meta property="og:site_name" content="{{ $seo['site_name'] ?? 'MeaCash' }}">
<meta property="og:locale" content="{{ $seo['locale'] ?? 'en_US' }}">

<!-- Twitter -->
<meta name="twitter:card" content="{{ $settings->get('twitter_card_type', 'summary_large_image') }}">
<meta name="twitter:url" content="{{ $seo['url'] ?? Request::fullUrl() }}">
<meta name="twitter:title" content="{{ $seo['title'] }}">
@if(!empty($seo['description']))
    <meta name="twitter:description" content="{{ $seo['description'] }}">
@endif
@if(!empty($seo['image']))
    <meta name="twitter:image" content="{{ $seo['image'] }}">
@endif
@if(!empty($seo['twitter_handle']))
    <meta name="twitter:site" content="{{ $seo['twitter_handle'] }}">
@endif

<!-- Verification Tags -->
@if($facebookVerify = $settings->get('facebook_domain_verification'))
    <meta name="facebook-domain-verification" content="{{ $facebookVerify }}">
@endif
@if($googleVerify = $settings->get('google_site_verification'))
    <meta name="google-site-verification" content="{{ $googleVerify }}">
@endif

<!-- Hreflang Tags -->
@foreach($seoService->buildHreflang() as $lang => $href)
    <link rel="alternate" hreflang="{{ $lang }}" href="{{ $href }}">
@endforeach

<!-- Structured Data -->
@if($settings->get('breadcrumb_schema_enabled', '1') == '1')
    <script type="application/ld+json">
        {!! $seoService->buildJsonLd($seo) !!}
    </script>
@endif

<!-- Analytics & Tracking -->
@if($gtmId = $settings->get('google_tag_manager_id'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    <!-- End Google Tag Manager -->
@endif

@if($gaId = $settings->get('google_analytics_id'))
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $gaId }}');
    </script>
@endif

@if($fbPixId = $settings->get('facebook_pixel_id'))
    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '{{ $fbPixId }}');
      fbq('track', 'PageView');
    </script>
@endif

@if($tiktokPixId = $settings->get('tiktok_pixel_id'))
    <!-- TikTok Pixel Code -->
    <script>
    !function (w, d, t) {
      w.ttq = w.ttq || [];
      w.ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias", "group", "aliasProp", "setAnonymousId", "updateSafeId"];
      w.ttq.setAndVerifyUrls = function (a, b) {
        var c = w.ttq;
        c._u = a;
        c._v = b;
        return c
      };
      w.ttq.load = function (e, i) {
        var t = "https://analytics.tiktok.com/i18n/pixel/events.js";
        w.ttq._i = w.ttq._i || {};
        w.ttq._i[e] = [];
        w.ttq._i[e]._u = t;
        w.ttq._t = w.ttq._t || {};
        w.ttq._t[e] = +new Date;
        w.ttq._o = w.ttq._o || {};
        w.ttq._o[e] = i || {};
        n = d.createElement("script");
        n.type = "text/javascript";
        n.async = !0;
        n.src = t + "?sdkid=" + e + "&lib=" + t;
        f = d.getElementsByTagName("script")[0];
        f.parentNode.insertBefore(n, f)
      };
      w.ttq.load('{{ $tiktokPixId }}');
      w.ttq.page();
    }(window, document, 'ttq');
    </script>
@endif

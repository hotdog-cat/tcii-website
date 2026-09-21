<!doctype html>
<html lang="en">
<head>
    @php
        $siteUrl = 'https://techtonic.ph/';
        $siteTitle = 'Techtonic Concrete Industries Inc. | Ready-Mixed Concrete in Bacolod';
        $siteDescription = 'Techtonic Concrete Industries Inc. supplies reliable ready-mixed concrete, concrete products, batching facilities, and project support across Bacolod City and Negros Occidental.';
        $siteImage = asset('images/techtonic-logo.png');
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => ['Organization', 'LocalBusiness'],
            'name' => 'Techtonic Concrete Industries Inc.',
            'url' => $siteUrl,
            'logo' => $siteImage,
            'image' => $siteImage,
            'description' => $siteDescription,
            'email' => 'techtonicrmc@gmail.com',
            'telephone' => ['034-213-0490', '034-461-9194', '0998-476-2210', '0918-664-0085', '0936-923-3732'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Purok Paho, Brgy. Felisa',
                'addressLocality' => 'Bacolod City',
                'addressRegion' => 'Negros Occidental',
                'postalCode' => '6100',
                'addressCountry' => 'PH',
            ],
            'areaServed' => ['Bacolod City', 'Negros Occidental'],
            'foundingDate' => '2020-09-03',
            'sameAs' => [
                'https://www.findglocal.com/PH/Bacolod-City/108014201371953/Techtonic-Concrete-Industries-Inc.',
            ],
        ];
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $siteDescription }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $siteUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $siteUrl }}">
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta property="og:image" content="{{ $siteImage }}">
    <meta property="og:locale" content="en_PH">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $siteTitle }}">
    <meta name="twitter:description" content="{{ $siteDescription }}">
    <meta name="twitter:image" content="{{ $siteImage }}">
    <title>{{ $siteTitle }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <script type="application/ld+json">@json($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    <script>window.__TECHTONIC_CONTENT__ = @json($contentJson);</script>
    <script>window.__TECHTONIC_PAGE_CONTENT__ = @json($pageContent);</script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/main.tsx'])
</head>
<body>
    <div id="root">
        <main>
            <h1>Techtonic Concrete Industries Inc.</h1>
            <p>Reliable ready-mixed concrete, concrete products, batching facilities, equipment support, and project supply services for Bacolod City and Negros Occidental.</p>
            <section>
                <h2>Ready-Mixed Concrete Supplier in Bacolod</h2>
                <p>Established on September 3, 2020, Techtonic Concrete Industries Inc. supports public and private construction projects with quality-controlled concrete and dependable delivery.</p>
            </section>
            <section>
                <h2>Contact Techtonic Concrete Industries Inc.</h2>
                <address>
                    Purok Paho, Brgy. Felisa, Bacolod City, Negros Occidental, Philippines 6100<br>
                    Email: <a href="mailto:techtonicrmc@gmail.com">techtonicrmc@gmail.com</a><br>
                    Telephone: <a href="tel:+63342130490">034-213-0490</a>, <a href="tel:+63344619194">034-461-9194</a>
                </address>
            </section>
        </main>
    </div>
</body>
</html>

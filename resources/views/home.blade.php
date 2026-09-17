@extends('layouts.site')

@section('content')
@php
    $facilities = $content->get('facility', collect());
    $equipment = $content->get('equipment', collect());
    $products = $content->get('product', collect());
    $projects = $content->get('project', collect());
    $team = $content->get('team', collect());
    $leaders = $team->filter(fn($item) => $item->description !== 'BOARD')->take(4);
    $board = $team->filter(fn($item) => $item->description === 'BOARD');
    $registry = $content->get('registry', collect());
@endphp
<main>
    <header class="site-header">
        <a class="brand" href="#home" aria-label="Techtonic home"><img src="{{ asset('images/techtonic-logo-white.png') }}" alt="Techtonic Concrete Industries Inc."></a>
        <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-navigation"><span></span><span></span></button>
        <nav id="primary-navigation" class="main-nav" aria-label="Primary navigation">
            <span class="mobile-menu-label">Navigation</span>
            <a href="#about">About Us</a>
            <a href="#mission">Mission &amp; Vision</a>
            @if($registry->isNotEmpty())<a href="#registry">Business Registry</a>@endif
            <a href="#team">Our Team</a>
            <div class="services-menu">
                <button type="button" aria-expanded="false">Services <span class="chevron" aria-hidden="true"></span></button>
                <div class="services-dropdown">
                    <a href="#facilities"><span>Facilities</span><span>↗</span></a>
                    <a href="#equipment"><span>Equipment</span><span>↗</span></a>
                    <a href="#products"><span>Products</span><span>↗</span></a>
                </div>
            </div>
            <a class="nav-contact" href="#contact">Contact Us</a>
        </nav>
    </header>

    <section class="hero" id="home">
        <div class="hero-media" aria-hidden="true"></div><div class="hero-shade" aria-hidden="true"></div>
        <div class="hero-content">
            <p class="eyebrow">Built for what comes next</p>
            <h1>Engineering Strength.<br>Delivering Certainty.</h1>
            <p class="hero-copy">Reliable ready-mixed concrete, engineered for enduring projects across Bacolod City and Negros Occidental.</p>
            <div class="hero-actions"><a class="button button-primary" href="#facilities">Explore Our Services <span>→</span></a><a class="text-link" href="#about">View Company Profile</a></div>
            <div class="trust-row"><span>Quality-Controlled</span><span>Reliable Delivery</span><span>Built to Specification</span></div>
        </div>
        <a class="scroll-cue" href="#about"><span>Discover</span><i></i></a>
    </section>

    <section class="about-section" id="about">
        <div class="section-heading"><p class="eyebrow">About Techtonic</p><h2>Concrete confidence,<br>from the ground up.</h2></div>
        <div class="about-copy">
            <p>Established on September 3, 2020, Techtonic Concrete Industries Inc. supplies ready-mixed concrete for public and private projects throughout Bacolod City and Negros Occidental.</p>
            <p>From roads and bridges to malls and buildings, our computerized wet-mix batching plant and skilled team bring accuracy, consistency, and dependable service to every pour.</p>
            <div class="about-stats"><div><strong>90</strong><span>cu. m. hourly plant capacity</span></div><div><strong>12</strong><span>transit mixers in the profile fleet</span></div><div><strong>2020</strong><span>year established</span></div></div>
        </div>
    </section>

    <section class="mission-section" id="mission">
        <div class="mission-intro"><p class="eyebrow">Mission &amp; Vision</p><h2>Measured by quality.<br>Driven by service.</h2><p>Every batch, delivery, and customer relationship is guided by a clear standard: deliver dependable concrete with accuracy and care.</p></div>
        <div class="mission-cards">
            <article><span>Our Mission</span><h3>Great service. Exceptional ready-mixed concrete.</h3><p>To provide our customers with excellent service and produce high-quality ready-mixed concrete that meets their expectations.</p></article>
            <article><span>Our Vision</span><h3>To lead through service, accuracy, and quality.</h3><p>To be the top supplier of ready-mixed concrete, recognized for dependable service, precise production, and consistent quality.</p></article>
        </div>
    </section>

    @if($registry->isNotEmpty())
    <section class="registry-section" id="registry">
        <div class="section-topline"><div><p class="eyebrow">Business Registry</p><h2>Built on verified standards.</h2></div><p>Registered, accredited, and supported by documented quality and calibration controls.</p></div>
        <div class="registry-grid">@foreach($registry as $item)<article class="registry-card"><div class="registry-image">@if($item->image_url)<img src="{{ $item->image_url }}" alt="{{ $item->title }} document" loading="lazy">@endif</div><div><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $item->title }}</h3><p>{{ $item->subtitle }}</p></div></article>@endforeach</div>
    </section>
    @endif

    <section class="team-section" id="team">
        <div class="section-topline"><div><p class="eyebrow">Our Team</p><h2>Experienced people.<br>One concrete standard.</h2></div><p>Leadership, technical oversight, and operational discipline working together on every project.</p></div>
        <div class="leader-grid">@foreach($leaders as $leader)<article class="leader-card"><div class="leader-mark">@if($leader->image_url)<img src="{{ $leader->image_url }}" alt="{{ $leader->title }}" loading="lazy">@else<span>{{ $leader->description ?: collect(explode(' ', $leader->title))->map(fn($p) => mb_substr($p,0,1))->take(2)->join('') }}</span>@endif<i>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</i></div><h3>{{ $leader->title }}</h3><p>{{ $leader->subtitle }}</p></article>@endforeach</div>
        @if($board->isNotEmpty())<div class="board-list"><p>Owners &amp; Board</p><div>@foreach($board as $member)<article><h3>{{ $member->title }}</h3><span>{{ $member->subtitle }}</span></article>@endforeach</div></div>@endif
    </section>

    <section class="showcase-section facilities-section" id="facilities">
        <div class="section-topline"><div><p class="eyebrow">Our Facilities</p><h2>Purpose-built for precision.</h2></div><p>A connected production environment designed for accurate batching, controlled testing, and reliable supply.</p></div>
        <div class="showcase-grid">@foreach($facilities as $item)<article class="showcase-card showcase-{{ $loop->iteration }}">@if($item->image_url)<img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy">@endif<div><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $item->title }}</h3><p>{{ $item->subtitle }}</p></div></article>@endforeach</div>
    </section>

    <section class="equipment-section" id="equipment">
        <div class="section-topline light"><div><p class="eyebrow">Our Equipment</p><h2>Capacity that keeps<br>projects moving.</h2></div><p>A coordinated fleet of transit mixers, pumps, and heavy equipment supports concrete delivery from plant to placement.</p></div>
        <div class="equipment-grid">@foreach($equipment as $item)<article><div class="equipment-image">@if($item->image_url)<img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy">@endif<span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div><h3>{{ $item->title }}</h3><p>{{ $item->subtitle }}</p></article>@endforeach</div>
    </section>

    <section class="products-section" id="products">
        <div class="products-heading"><p class="eyebrow">Our Products</p><h2>Concrete designed around the demands of the job.</h2></div>
        <div><div class="product-list">@foreach($products as $item)<article><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $item->title }}</h3><p>{{ $item->description ?? $item->subtitle }}</p></div></article>@endforeach</div><a class="button button-primary product-cta" href="#contact">Discuss Your Requirements <span>→</span></a></div>
    </section>

    <section class="projects-section" aria-labelledby="projects-title">
        <div class="section-topline"><div><p class="eyebrow">Selected Projects</p><h2 id="projects-title">Proof in every pour.</h2></div><p>Real project work featured in the Techtonic company profile across Bacolod City and Negros Occidental.</p></div>
        <div class="project-grid">@foreach($projects as $project)<article>@if($project->image_url)<img src="{{ $project->image_url }}" alt="{{ $project->title }}" loading="lazy">@endif<div><h3>{{ $project->title }}</h3><span>{{ $project->subtitle }}</span></div></article>@endforeach</div>
    </section>

    <section class="contact-section" id="contact">
        <div class="contact-main"><p class="eyebrow">Contact Us</p><h2>Let's build something<br>that lasts.</h2><p>Tell us about your concrete requirements, schedule, and project location. Our team is ready to help.</p>
            @if(session('success'))<div class="form-alert success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="form-alert error">Please check the highlighted fields and try again.</div>@endif
            <form class="contact-form" action="{{ route('contact.store') }}" method="post">@csrf
                <div class="form-row"><label>Name<input name="name" value="{{ old('name') }}" required></label><label>Email<input type="email" name="email" value="{{ old('email') }}" required></label></div>
                <div class="form-row"><label>Phone<input name="phone" value="{{ old('phone') }}"></label><label>Company<input name="company" value="{{ old('company') }}"></label></div>
                <label>Subject<input name="subject" value="{{ old('subject') }}"></label><label>Project Requirements<textarea name="message" rows="5" required>{{ old('message') }}</textarea></label>
                <button class="button button-primary" type="submit">Send Inquiry <span>→</span></button>
            </form>
        </div>
        <div class="contact-details"><div><span>Office Address</span><p>Purok Paho, Brgy. Felisa<br>Bacolod City, Negros Occidental<br>Philippines 6100</p></div><div><span>Telephone</span><p><a href="tel:+63342130490">034-213-0490</a><br><a href="tel:+63344619194">034-461-9194</a></p></div><div><span>Mobile</span><p><a href="tel:+639984762210">0998-476-2210</a><br><a href="tel:+639186640085">0918-664-0085</a><br><a href="tel:+639369233732">0936-923-3732</a></p></div><div><span>Email</span><p><a href="mailto:techtonicrmc@gmail.com">techtonicrmc@gmail.com</a></p></div></div>
    </section>

    <footer>
        <div class="footer-intro"><a class="footer-brand" href="#home"><img src="{{ asset('images/techtonic-logo-white.png') }}" alt="Techtonic Concrete Industries Inc."></a><p>Reliable concrete. Built for what comes next.</p></div>
        <div class="footer-links"><h2>Quick Links</h2><nav><a href="#about">About Us</a><a href="#mission">Mission &amp; Vision</a>@if($registry->isNotEmpty())<a href="#registry">Business Registry</a>@endif<a href="#team">Our Team</a><a href="#facilities">Facilities</a><a href="#equipment">Equipment</a><a href="#products">Products</a><a href="#contact">Contact Us</a></nav></div>
        <div class="footer-contact"><h2>Get in Touch</h2><a href="mailto:techtonicrmc@gmail.com">techtonicrmc@gmail.com</a><a href="tel:+63342130490">034-213-0490</a><p>Purok Paho, Brgy. Felisa<br>Bacolod City, Negros Occidental</p></div>
        <div class="footer-bottom"><span>© {{ date('Y') }} Techtonic Concrete Industries Inc.</span><a href="#home">Back to top ↑</a></div>
    </footer>
</main>
@endsection

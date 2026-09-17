@extends('layouts.admin')
@section('title', 'Page Content')
@section('content')
@php
    $activeTab = in_array(request('tab'), ['text', 'maintenance', 'banner', 'header-logo', 'footer-logo'], true) ? request('tab') : 'text';
    $field = fn (string $name, string $label, string $type = 'input', ?string $hint = null) => compact('name', 'label', 'type', 'hint');
    $optionalFields = ['contact_telephones'];
    $sections = [
        ['id' => 'navigation', 'title' => 'Navigation', 'fields' => [
            $field('navigation_label', 'Mobile menu label'), $field('nav_about', 'About link'),
            $field('nav_mission', 'Mission & Vision link'), $field('nav_registry', 'Business Registry link'),
            $field('nav_team', 'Team link'), $field('nav_services', 'Services menu label'),
            $field('nav_facilities', 'Facilities link'), $field('nav_equipment', 'Equipment link'),
            $field('nav_products', 'Products link'), $field('nav_contact', 'Contact link'),
        ]],
        ['id' => 'hero', 'title' => 'Hero', 'fields' => [
            $field('hero_eyebrow', 'Section label'),
            $field('hero_heading', 'Main heading', 'textarea', 'Start a new line where you want the heading to break.'),
            $field('hero_copy', 'Introductory paragraph', 'long'),
            $field('hero_primary_button', 'Primary button'), $field('hero_secondary_button', 'Secondary link'),
            $field('hero_trust_one', 'Trust point 1'), $field('hero_trust_two', 'Trust point 2'),
            $field('hero_trust_three', 'Trust point 3'), $field('hero_scroll_label', 'Scroll prompt'),
        ]],
        ['id' => 'about', 'title' => 'About Techtonic', 'fields' => [
            $field('about_eyebrow', 'Section label'),
            $field('about_heading', 'Main heading', 'textarea', 'Start a new line where you want the heading to break.'),
            $field('about_paragraph_one', 'First paragraph', 'long'), $field('about_paragraph_two', 'Second paragraph', 'long'),
            $field('about_stat_one_value', 'Statistic 1 value'), $field('about_stat_one_label', 'Statistic 1 label'),
            $field('about_stat_two_value', 'Statistic 2 value'), $field('about_stat_two_label', 'Statistic 2 label'),
            $field('about_stat_three_value', 'Statistic 3 value'), $field('about_stat_three_label', 'Statistic 3 label'),
        ]],
        ['id' => 'mission', 'title' => 'Mission & Vision', 'fields' => [
            $field('mission_eyebrow', 'Section label'),
            $field('mission_heading', 'Main heading', 'textarea', 'Start a new line where you want the heading to break.'),
            $field('mission_intro', 'Introductory paragraph', 'long'),
            $field('mission_card_label', 'Mission card label'), $field('mission_title', 'Mission heading', 'textarea'),
            $field('mission_description', 'Mission description', 'long'),
            $field('vision_card_label', 'Vision card label'), $field('vision_title', 'Vision heading', 'textarea'),
            $field('vision_description', 'Vision description', 'long'),
        ]],
        ['id' => 'registry', 'title' => 'Business Registry', 'fields' => [
            $field('registry_eyebrow', 'Section label'), $field('registry_heading', 'Main heading', 'textarea'),
            $field('registry_intro', 'Introductory paragraph', 'long'),
        ]],
        ['id' => 'team', 'title' => 'Team', 'fields' => [
            $field('team_eyebrow', 'Section label'),
            $field('team_heading', 'Main heading', 'textarea', 'Start a new line where you want the heading to break.'),
            $field('team_intro', 'Introductory paragraph', 'long'), $field('team_board_label', 'Owners and board label'),
        ]],
        ['id' => 'facilities', 'title' => 'Facilities', 'fields' => [
            $field('facilities_eyebrow', 'Section label'), $field('facilities_heading', 'Main heading', 'textarea'),
            $field('facilities_intro', 'Introductory paragraph', 'long'),
        ]],
        ['id' => 'equipment', 'title' => 'Equipment', 'fields' => [
            $field('equipment_eyebrow', 'Section label'),
            $field('equipment_heading', 'Main heading', 'textarea', 'Start a new line where you want the heading to break.'),
            $field('equipment_intro', 'Introductory paragraph', 'long'),
        ]],
        ['id' => 'products', 'title' => 'Products', 'fields' => [
            $field('products_eyebrow', 'Section label'), $field('products_heading', 'Main heading', 'textarea'),
            $field('products_button_label', 'Contact button label'),
        ]],
        ['id' => 'projects', 'title' => 'Projects', 'fields' => [
            $field('projects_eyebrow', 'Section label'), $field('projects_heading', 'Main heading', 'textarea'),
            $field('projects_intro', 'Introductory paragraph', 'long'),
        ]],
        ['id' => 'contact', 'title' => 'Contact Details', 'fields' => [
            $field('contact_eyebrow', 'Section label'),
            $field('contact_heading', 'Main heading', 'textarea', 'Start a new line where you want the heading to break.'),
            $field('contact_intro', 'Introductory paragraph', 'long'), $field('contact_button_label', 'Button label'),
            $field('contact_address_label', 'Address label'), $field('contact_telephone_label', 'Telephone label'),
            $field('contact_mobile_label', 'Mobile label'), $field('contact_email_label', 'Email label'),
            $field('contact_email', 'Email address', 'email'),
            $field('contact_address', 'Office address', 'long', 'Enter one address line per row.'),
            $field('contact_telephones', 'Telephone numbers', 'long', 'Enter one number per row.'),
            $field('contact_mobiles', 'Mobile numbers', 'long', 'Enter one number per row.'),
        ]],
        ['id' => 'inquiry', 'title' => 'Inquiry Form', 'fields' => [
            $field('inquiry_eyebrow', 'Form label'),
            $field('inquiry_heading', 'Form heading', 'textarea', 'Start a new line where you want the heading to break.'),
            $field('inquiry_name_label', 'Name field label'), $field('inquiry_email_label', 'Email field label'),
            $field('inquiry_phone_label', 'Phone field label'), $field('inquiry_company_label', 'Company field label'),
            $field('inquiry_subject_label', 'Subject field label'), $field('inquiry_message_label', 'Message field label'),
            $field('inquiry_submit_label', 'Submit button label'), $field('inquiry_sending_label', 'Sending state label'),
            $field('inquiry_success_message', 'Success message', 'long'), $field('inquiry_error_message', 'Fallback error message', 'long'),
        ]],
        ['id' => 'footer', 'title' => 'Footer', 'fields' => [
            $field('footer_tagline', 'Company tagline'), $field('footer_links_heading', 'Quick links heading'),
            $field('footer_contact_heading', 'Contact heading'), $field('footer_company_name', 'Copyright company name'),
            $field('footer_back_to_top', 'Back-to-top label'),
        ]],
    ];
@endphp

<div class="page-heading">
    <div>
        <p class="kicker">Website Content</p>
        <h1>Page Content</h1>
        <p>Edit all fixed text shown on the public website. Repeatable cards remain in their individual content managers.</p>
    </div>
</div>

<div class="content-tabs" role="tablist" aria-label="Page content settings">
    @foreach(['text' => 'Page Text', 'maintenance' => 'Maintenance', 'banner' => 'Home Banner', 'header-logo' => 'Header Logo', 'footer-logo' => 'Footer Logo'] as $tab => $label)
        <button @class(['active' => $activeTab === $tab]) type="button" role="tab" aria-selected="{{ $activeTab === $tab ? 'true' : 'false' }}" data-content-tab="{{ $tab }}">{{ $label }}</button>
    @endforeach
</div>

<form class="page-content-form" method="post" enctype="multipart/form-data" action="{{ route('admin.page-content.update') }}">
    @csrf
    @method('PUT')

    <div @class(['content-tab-panel', 'active' => $activeTab === 'text']) role="tabpanel" data-content-panel="text" @if($activeTab !== 'text') hidden @endif>
        <nav class="content-jump-links" aria-label="Page text sections">
            @foreach($sections as $section)<a href="#{{ $section['id'] }}">{{ $section['title'] }}</a>@endforeach
        </nav>

        @foreach($sections as $index => $section)
            @php
                $visibilityKey = 'section_'.$section['id'].'_published';
            @endphp
            <section class="panel form-panel content-section-panel" id="{{ $section['id'] }}">
                <div class="panel-title">
                    <div><p class="kicker">Section {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p><h2>{{ $section['title'] }}</h2></div>
                    <div class="section-panel-actions">
                        @if(array_key_exists($section['id'], \App\Models\PageContent::SECTION_VISIBILITY))
                            <label>
                                <span class="sr-only">{{ $section['title'] }} publication status</span>
                                <select name="{{ $visibilityKey }}">
                                    <option value="1" @selected(old($visibilityKey, $content[$visibilityKey]) === '1')>Published</option>
                                    <option value="0" @selected(old($visibilityKey, $content[$visibilityKey]) === '0')>Draft</option>
                                </select>
                            </label>
                        @endif
                        <span class="fixed-layout-note">Content only &middot; Layout is fixed</span>
                    </div>
                </div>
                <div class="form-grid">
                    @foreach($section['fields'] as $item)
                        @php
                            $isTextarea = in_array($item['type'], ['textarea', 'long'], true);
                            $isWide = $isTextarea || $item['name'] === 'contact_address';
                            $maxLength = $item['type'] === 'long' ? 3000 : 190;
                            $isRequired = ! in_array($item['name'], $optionalFields, true);
                        @endphp
                        <label @class(['wide' => $isWide])>
                            {{ $item['label'] }}
                            @if($isTextarea)
                                <textarea name="{{ $item['name'] }}" rows="{{ $item['type'] === 'long' ? 4 : 2 }}" maxlength="{{ $maxLength }}" @required($isRequired)>{{ old($item['name'], $content[$item['name']]) }}</textarea>
                            @else
                                <input type="{{ $item['type'] === 'email' ? 'email' : 'text' }}" name="{{ $item['name'] }}" maxlength="{{ $maxLength }}" value="{{ old($item['name'], $content[$item['name']]) }}" @required($isRequired)>
                            @endif
                            @if($item['hint'])<small>{{ $item['hint'] }}</small>@endif
                        </label>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <div @class(['content-tab-panel', 'active' => $activeTab === 'maintenance']) role="tabpanel" data-content-panel="maintenance" @if($activeTab !== 'maintenance') hidden @endif>
        <section class="panel maintenance-settings-panel">
            <div class="panel-title">
                <div>
                    <p class="kicker">Website Availability</p>
                    <h2>Maintenance Mode</h2>
                </div>
            </div>
            <div class="maintenance-settings-grid">
                <div class="maintenance-settings-copy">
                    <label>
                        Public website status
                        <select name="maintenance_enabled">
                            <option value="0" @selected(old('maintenance_enabled', $content['maintenance_enabled']) === '0')>Live</option>
                            <option value="1" @selected(old('maintenance_enabled', $content['maintenance_enabled']) === '1')>Under Maintenance</option>
                        </select>
                        <small>Admin pages remain available so maintenance mode can be turned off anytime.</small>
                    </label>
                    <label>
                        Maintenance heading
                        <input type="text" name="maintenance_heading" maxlength="190" value="{{ old('maintenance_heading', $content['maintenance_heading']) }}" required>
                    </label>
                    <label>
                        Urgent matters message
                        <textarea name="maintenance_message" rows="3" maxlength="3000" required>{{ old('maintenance_message', $content['maintenance_message']) }}</textarea>
                        <small>The saved contact details below will be shown automatically.</small>
                    </label>
                </div>
                <div class="maintenance-preview">
                    <img src="{{ $content['header_logo_image'] }}" alt="Current website logo">
                    <h3>{{ old('maintenance_heading', $content['maintenance_heading']) }}</h3>
                    <p>{{ old('maintenance_message', $content['maintenance_message']) }}</p>
                    <dl>
                        <div><dt>{{ $content['contact_address_label'] }}</dt><dd>{!! nl2br(e($content['contact_address'])) !!}</dd></div>
                        @if(trim($content['contact_telephones']) !== '')<div><dt>{{ $content['contact_telephone_label'] }}</dt><dd>{!! nl2br(e($content['contact_telephones'])) !!}</dd></div>@endif
                        <div><dt>{{ $content['contact_mobile_label'] }}</dt><dd>{!! nl2br(e($content['contact_mobiles'])) !!}</dd></div>
                        <div><dt>{{ $content['contact_email_label'] }}</dt><dd>{{ $content['contact_email'] }}</dd></div>
                    </dl>
                </div>
            </div>
        </section>
    </div>

    @foreach([
        ['tab' => 'banner', 'title' => 'Home Banner', 'description' => 'The background image displayed across the home-page hero.', 'key' => 'hero_background_image', 'upload' => 'hero_background_upload', 'recommendation' => 'Recommended: wide landscape image, at least 1920 × 1080 pixels.', 'class' => 'banner-preview', 'size_key' => null],
        ['tab' => 'header-logo', 'title' => 'Header Logo', 'description' => 'The logo displayed in the main website header.', 'key' => 'header_logo_image', 'upload' => 'header_logo_upload', 'recommendation' => 'Recommended: transparent PNG with a wide logo layout.', 'class' => 'logo-preview', 'size_key' => 'header_logo_width'],
        ['tab' => 'footer-logo', 'title' => 'Footer Logo', 'description' => 'The logo displayed in the website footer.', 'key' => 'footer_logo_image', 'upload' => 'footer_logo_upload', 'recommendation' => 'Recommended: transparent PNG that remains legible on a dark background.', 'class' => 'logo-preview', 'size_key' => 'footer_logo_width'],
    ] as $media)
        <div @class(['content-tab-panel', 'active' => $activeTab === $media['tab']]) role="tabpanel" data-content-panel="{{ $media['tab'] }}" @if($activeTab !== $media['tab']) hidden @endif>
            <section class="panel media-settings-panel">
                <div class="panel-title"><div><p class="kicker">Website Media</p><h2>{{ $media['title'] }}</h2></div></div>
                <div class="media-settings-grid">
                    <div class="media-preview {{ $media['class'] }}"><img src="{{ $content[$media['key']] }}" alt="Current {{ strtolower($media['title']) }}"></div>
                    <div class="media-upload-copy">
                        <h3>Replace {{ $media['title'] }}</h3>
                        <p>{{ $media['description'] }}</p>
                        <label>Select a new image<input type="file" name="{{ $media['upload'] }}" accept="image/png,image/jpeg,image/webp,image/gif"></label>
                        <small>{{ $media['recommendation'] }}</small>
                        <button
                            class="danger-btn remove-image-btn"
                            type="submit"
                            name="remove_media"
                            value="{{ $media['key'] }}"
                            formnovalidate
                            onclick="return confirm('Remove this image and restore the default?')"
                        >Remove Image</button>
                        @if($media['size_key'])
                            <div class="logo-size-control" data-logo-size-control>
                                <div><strong>Logo width</strong><output data-logo-size-output>{{ old($media['size_key'], $content[$media['size_key']]) }} px</output></div>
                                <input type="range" min="100" max="400" step="1" value="{{ old($media['size_key'], $content[$media['size_key']]) }}" data-logo-size-range>
                                <label>Exact width in pixels<input type="number" name="{{ $media['size_key'] }}" min="100" max="400" step="1" value="{{ old($media['size_key'], $content[$media['size_key']]) }}" required data-logo-size-number></label>
                            </div>
                        @endif
                        <input type="hidden" name="{{ $media['key'] }}" value="{{ old($media['key'], $content[$media['key']]) }}">
                    </div>
                </div>
            </section>
        </div>
    @endforeach

    <div class="sticky-form-actions">
        <span>Changes appear on the public website after saving.</span>
        <button class="primary-btn" type="submit">Save Page Content</button>
    </div>
</form>
<script>
document.querySelectorAll('[data-content-tab]').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('[data-content-tab]').forEach(tab => {
        const active = tab === button;
        tab.classList.toggle('active', active);
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    document.querySelectorAll('[data-content-panel]').forEach(panel => {
        const active = panel.dataset.contentPanel === button.dataset.contentTab;
        panel.classList.toggle('active', active);
        panel.hidden = !active;
    });
    const url = new URL(window.location.href);
    if (button.dataset.contentTab === 'text') url.searchParams.delete('tab');
    else url.searchParams.set('tab', button.dataset.contentTab);
    window.history.replaceState({}, '', url);
}));
document.querySelectorAll('[data-logo-size-control]').forEach(control => {
    const range = control.querySelector('[data-logo-size-range]');
    const number = control.querySelector('[data-logo-size-number]');
    const output = control.querySelector('[data-logo-size-output]');
    const preview = control.closest('.media-settings-grid').querySelector('.media-preview img');
    const update = value => {
        const size = Math.min(400, Math.max(100, Number(value) || 100));
        range.value = size;
        number.value = size;
        output.textContent = `${size} px`;
        preview.style.width = `${size}px`;
    };
    range.addEventListener('input', () => update(range.value));
    number.addEventListener('input', () => update(number.value));
    update(number.value);
});
</script>
@endsection

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    public const DEFAULTS = [
        'hero_background_image' => '/images/hero-night.webp',
        'header_logo_image' => '/images/techtonic-logo-white.png',
        'footer_logo_image' => '/images/techtonic-logo-white.png',
        'header_logo_width' => '252',
        'footer_logo_width' => '205',
        'navigation_label' => 'Navigation',
        'nav_about' => 'About Us',
        'nav_mission' => 'Mission & Vision',
        'nav_registry' => 'Business Registry',
        'nav_team' => 'Our Team',
        'nav_services' => 'Services',
        'nav_facilities' => 'Facilities',
        'nav_equipment' => 'Equipment',
        'nav_products' => 'Products',
        'nav_contact' => 'Contact Us',
        'hero_eyebrow' => 'Built for what comes next',
        'hero_heading' => "Engineering Strength.\nDelivering Certainty.",
        'hero_copy' => 'Reliable ready-mixed concrete, engineered for enduring projects across Bacolod City and Negros Occidental.',
        'hero_primary_button' => 'Explore Our Services',
        'hero_secondary_button' => 'View Company Profile',
        'hero_trust_one' => 'Quality-Controlled',
        'hero_trust_two' => 'Reliable Delivery',
        'hero_trust_three' => 'Built to Specification',
        'hero_scroll_label' => 'Discover',
        'about_eyebrow' => 'About Techtonic',
        'about_heading' => "Concrete confidence,\nfrom the ground up.",
        'about_paragraph_one' => 'Established on September 3, 2020, Techtonic Concrete Industries Inc. supplies ready-mixed concrete for public and private projects throughout Bacolod City and Negros Occidental.',
        'about_paragraph_two' => 'From roads and bridges to malls and buildings, our computerized wet-mix batching plant and skilled team bring accuracy, consistency, and dependable service to every pour.',
        'about_stat_one_value' => '90',
        'about_stat_one_label' => 'cu. m. hourly plant capacity',
        'about_stat_two_value' => '12',
        'about_stat_two_label' => 'transit mixers in the profile fleet',
        'about_stat_three_value' => '2020',
        'about_stat_three_label' => 'year established',
        'mission_eyebrow' => 'Mission & Vision',
        'mission_heading' => "Measured by quality.\nDriven by service.",
        'mission_intro' => 'Every batch, delivery, and customer relationship is guided by a clear standard: deliver dependable concrete with accuracy and care.',
        'mission_title' => 'Great service. Exceptional ready-mixed concrete.',
        'mission_description' => 'To provide our customers with excellent service and produce high-quality ready-mixed concrete that meets their expectations.',
        'mission_card_label' => 'Our Mission',
        'vision_card_label' => 'Our Vision',
        'vision_title' => 'To lead through service, accuracy, and quality.',
        'vision_description' => 'To be the top supplier of ready-mixed concrete, recognized for dependable service, precise production, and consistent quality.',
        'registry_eyebrow' => 'Business Registry',
        'registry_heading' => 'Built on verified standards.',
        'registry_intro' => 'Registered, accredited, and supported by documented quality and calibration controls.',
        'team_eyebrow' => 'Our Team',
        'team_heading' => "Experienced people.\nOne concrete standard.",
        'team_intro' => 'Leadership, technical oversight, and operational discipline working together on every project.',
        'team_board_label' => 'Owners & Board',
        'facilities_eyebrow' => 'Our Facilities',
        'facilities_heading' => 'Purpose-built for precision.',
        'facilities_intro' => 'A connected production environment designed for accurate batching, controlled testing, and reliable supply.',
        'equipment_eyebrow' => 'Our Equipment',
        'equipment_heading' => "Capacity that keeps\nprojects moving.",
        'equipment_intro' => 'A coordinated fleet of transit mixers, pumps, and heavy equipment supports concrete delivery from plant to placement.',
        'products_eyebrow' => 'Our Products',
        'products_heading' => 'Concrete designed around the demands of the job.',
        'products_button_label' => 'Discuss Your Requirements',
        'projects_eyebrow' => 'Selected Projects',
        'projects_heading' => 'Proof in every pour.',
        'projects_intro' => 'Real project work featured in the Techtonic company profile across Bacolod City and Negros Occidental.',
        'contact_eyebrow' => 'Contact Us',
        'contact_heading' => "Let's build something\nthat lasts.",
        'contact_intro' => 'Tell us about your concrete requirements, schedule, and project location. Our team is ready to help.',
        'contact_button_label' => 'Send an Inquiry',
        'contact_address' => "Purok Paho, Brgy. Felisa\nBacolod City, Negros Occidental\nPhilippines 6100",
        'contact_telephones' => "034-213-0490\n034-461-9194",
        'contact_mobiles' => "0998-476-2210\n0918-664-0085\n0936-923-3732",
        'contact_email' => 'techtonicrmc@gmail.com',
        'contact_address_label' => 'Office Address',
        'contact_telephone_label' => 'Telephone',
        'contact_mobile_label' => 'Mobile',
        'contact_email_label' => 'Email',
        'inquiry_eyebrow' => 'Project Inquiry',
        'inquiry_heading' => "Tell us what\nyou're building.",
        'inquiry_name_label' => 'Name',
        'inquiry_email_label' => 'Email',
        'inquiry_phone_label' => 'Phone',
        'inquiry_company_label' => 'Company',
        'inquiry_subject_label' => 'Subject',
        'inquiry_message_label' => 'Project requirements',
        'inquiry_submit_label' => 'Send Inquiry',
        'inquiry_sending_label' => 'Sending...',
        'inquiry_success_message' => 'Thank you. Your inquiry has been sent to our team.',
        'inquiry_error_message' => 'Unable to send your inquiry. Please check your details and try again.',
        'footer_tagline' => 'Reliable concrete. Built for what comes next.',
        'footer_links_heading' => 'Quick Links',
        'footer_contact_heading' => 'Get in Touch',
        'footer_company_name' => 'Techtonic Concrete Industries Inc.',
        'footer_back_to_top' => 'Back to top',
    ];

    protected $fillable = ['key', 'value'];

    public static function values(): array
    {
        if (! static::query()->exists()) {
            return self::DEFAULTS;
        }

        return array_replace(
            self::DEFAULTS,
            static::query()->whereIn('key', array_keys(self::DEFAULTS))->pluck('value', 'key')->all()
        );
    }
}

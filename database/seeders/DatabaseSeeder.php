<?php

namespace Database\Seeders;

use App\Models\ContentItem;
use App\Models\PageContent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@company.com'],
            ['name' => 'Company Administrator', 'password' => Hash::make('password'), 'is_admin' => true]
        );

        foreach (PageContent::DEFAULTS as $key => $value) {
            PageContent::updateOrCreate(compact('key'), compact('value'));
        }

        $items = [
            ['facility', 'Batching Plant', 'Computerized wet-mix production', null, 'images/facility-batching.webp', 1, true],
            ['facility', 'Control Room', 'Accurate and monitored batching', null, 'images/facility-control.webp', 2, true],
            ['facility', 'Laboratory', 'On-site quality control', null, 'images/facility-laboratory.webp', 3, true],
            ['facility', 'Cement Warehouse', 'Organized materials storage', null, 'images/facility-warehouse.webp', 4, true],
            ['equipment', 'Transit Mixer Fleet', '6 and 10 cu. m. units', null, 'images/equipment-mixer.webp', 1, true],
            ['equipment', 'Concrete Pumps', 'Truck-mounted placement reach', null, 'images/equipment-pump.webp', 2, true],
            ['equipment', 'Payloader', 'Reliable yard operations', null, 'images/equipment-loader.webp', 3, true],
            ['equipment', 'Heavy Equipment', 'Project-ready support fleet', null, 'images/equipment-fleet.webp', 4, true],
            ['product', 'Quality Ready-Mixed Concrete', null, 'Consistent concrete supply for roads, bridges, malls, buildings, and other developments.', null, 1, true],
            ['product', 'Controlled Mix Production', null, 'Computerized wet-mix batching supports repeatable proportions, reliable output, and specification accuracy.', null, 2, true],
            ['product', 'High-Volume Project Supply', null, 'Up to 90 cubic meters per hour of plant capacity, backed by a coordinated mixer and pump fleet.', null, 3, true],
            ['project', 'SMDC - Smile Residences', 'Bacolod City', null, 'images/project-smile.webp', 1, true],
            ['project', 'Citadines Hotel', 'Bacolod City', null, 'images/project-citadines.webp', 2, true],
            ['project', 'J. Qua Construction', 'Negros Occidental', null, 'images/project-jqua.webp', 3, true],
            ['project', 'URC', 'Kabankalan', null, 'images/project-urc.webp', 4, true],
            ['team', 'Francis Victor R. Lamata', 'CEO / President', 'FL', null, 1, true],
            ['team', 'Renand T. Yutis', 'General Manager', 'RY', null, 2, true],
            ['team', 'Herbert A. Capangyarihan', 'Batching Plant Manager', 'HC', null, 3, true],
            ['team', 'Ismael P. Fuentes', 'QC Supervisor', 'IF', null, 4, true],
            ['team', 'Angelo Gabriel R. Lamata', 'Corporate Secretary', 'BOARD', null, 5, true],
            ['team', 'Jose Nilbert R. Lamata', 'Treasurer', 'BOARD', null, 6, true],
            ['team', 'Inna Concepcion R. Lamata', 'Board of Director', 'BOARD', null, 7, true],
            ['team', 'Adrian Joshua R. Lamata', 'Board of Director', 'BOARD', null, 8, true],
            ['team', 'Fatima Trisha R. Lamata', 'Board of Director', 'BOARD', null, 9, true],
            ['team', 'Wynona Daniella R. Lamata', 'Board of Director', 'BOARD', null, 10, true],
            ['registry', 'SEC Registration', 'Certificate of Incorporation', null, 'images/registry-sec.webp', 1, false],
            ['registry', 'BIR Registration', 'Registered business entity', null, 'images/registry-bir.webp', 2, false],
            ['registry', 'Laboratory Accreditation', 'Independent testing recognition', null, 'images/registry-accreditation.webp', 3, false],
            ['registry', 'Calibration Certificates', 'Verified batching accuracy', null, 'images/registry-calibration.webp', 4, false],
        ];

        foreach ($items as [$type, $title, $subtitle, $description, $image, $order, $published]) {
            ContentItem::updateOrCreate(
                ['type' => $type, 'title' => $title],
                compact('subtitle', 'description') + ['image_path' => $image, 'sort_order' => $order, 'is_published' => $published]
            );
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'person_information')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('person_information');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'person_information')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->text('person_information')->nullable()->after('profile_photo_path');
        });
    }
};

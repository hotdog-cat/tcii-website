<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(User::ROLE_EDITOR)->after('is_admin');
            $table->string('profile_photo_path')->nullable()->after('role');
            $table->string('contact_number')->nullable()->after('profile_photo_path');
        });

        DB::table('users')->where('is_admin', true)->update(['role' => User::ROLE_ADMIN]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_photo_path', 'contact_number']);
        });
    }
};

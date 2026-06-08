<?php

use App\Models\Album;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->string('access_code', 20)->nullable()->change();
            $table->uuid('share_token')->nullable()->unique()->after('access_code');
        });

        Album::whereNull('share_token')->each(fn ($a) => $a->update(['share_token' => Str::uuid()->toString()]));
    }

    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('share_token');
            $table->string('access_code', 8)->nullable()->change();
        });
    }
};

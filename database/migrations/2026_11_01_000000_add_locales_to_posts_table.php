<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('locale', 2)->default('pt');
            $table->uuid('translation_group_id')->nullable();
        });

        DB::table('posts')
            ->select('id')
            ->orderBy('id')
            ->each(function (object $post): void {
                DB::table('posts')
                    ->where('id', $post->id)
                    ->update(['translation_group_id' => (string) Str::uuid()]);
            });

        Schema::table('posts', function (Blueprint $table) {
            $table->unique(
                ['translation_group_id', 'locale'],
                'posts_translation_group_locale_unique',
            );
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropUnique('posts_translation_group_locale_unique');
            $table->dropIndex(['locale']);
            $table->dropColumn(['translation_group_id', 'locale']);
        });
    }
};

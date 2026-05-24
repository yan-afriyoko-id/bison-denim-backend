<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug', 250)->nullable()->after('name');
        });
        $brands = DB::table('brands')->get();

        foreach ($brands as $brand) {
            $baseSlug = Str::slug($brand->name);
            $slug = $baseSlug;
            $counter = 1;
            while (DB::table('brands')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            DB::table('brands')
                ->where('id', $brand->id)
                ->update(['slug' => $slug]);
        }
        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug', 250)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
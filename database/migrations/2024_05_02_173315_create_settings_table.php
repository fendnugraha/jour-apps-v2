<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name', 60)->default('Jour Apps');
            $table->string('address', 160)->default('Jl. Pahlawan No. 1');
            $table->string('telephone', 20)->default('08123456789');
            $table->string('email', 60)->default('admin@jour.com');
            $table->string('logo', 60)->default('logo.png');
            $table->string('favicon', 60)->default('favicon.png');
            $table->string('description', 160)->nullable();
            $table->string('cash_account', 160)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

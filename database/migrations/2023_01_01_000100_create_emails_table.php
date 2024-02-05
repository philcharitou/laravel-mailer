<?php

use App\Models\EmailAddress;
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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();

            $table->string('subject')->nullable();
            $table->longText('from')->nullable();
            $table->longText('to')->nullable();
            $table->longText('cc')->nullable();
            $table->longText('bcc')->nullable();
            $table->longText('body')->nullable();

            // Boolean(s)
            $table->boolean('is_draft')->default(true);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_archived')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};

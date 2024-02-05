<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');

            // Identification Field(s)
            $table->string('version_number')->nullable();
            $table->string('title')->nullable();
            $table->string('author')->nullable();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('format')->nullable();
            // File(s)
            $table->string('file');
            // Optional Field(s)
            $table->longText('description')->nullable();
            $table->longText('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}

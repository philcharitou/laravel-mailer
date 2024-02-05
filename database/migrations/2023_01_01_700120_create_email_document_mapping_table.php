<?php

use App\Models\Document;
use App\Models\Email;
use App\Models\Template;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateEmailDocumentMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_document_mapping', function (Blueprint $table) {
            $table->increments('id');

            // Identification Fields
            $table->foreignIdFor(Email::class);
            $table->foreignIdFor(Document::class);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_document_mapping');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_uses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('business_name');
            $table->integer('category_id');
            $table->string('email')->unique();
            $table->string('contact_person');
            $table->string('phone');
            $table->string('address');
            $table->string('city');
            $table->string('description');
            $table->string('file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_uses');
    }
}

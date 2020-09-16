<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBiginteger('user_id')->unsigned()->nullable(false)->index('user_id')->comment('用户id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('province_code')->unsigned()->nullable(true)->comment('省id');
            $table->string('province_name',20)->nullable(true)->comment('省');
            $table->integer('city_code')->unsigned()->nullable(true)->comment('市id');
            $table->string('city_name',20)->nullable(true)->comment('市');
            $table->integer('district_code')->unsigned()->nullable(true)->comment('区id');
            $table->string('district_name')->nullable(true)->comment('区');
            $table->string('strict',20)->nullable(true)->comment('街道');
            $table->string('zipcode',10)->nullable(true)->comment('邮编');
            $table->string('contact_name')->comment('收件人');
            $table->string('contact_phone',20)->comment('手机');
            $table->dateTime('last_used_at')->nullable(true)->comment('最后使用时间');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('addresses');

    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('attachmentable', function (Blueprint $table) {
            $table->string('group')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::create('attachmentable', function (Blueprint $table) {
            $table->dropColumn(['group']);
        });
    }
};

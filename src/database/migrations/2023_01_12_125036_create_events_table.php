<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_title')->comment('イベント名');
            $table->string('event_body')->nullable()->comment('イベント内容');
            $table->datetime('start_date')->comment('開始日時');
            $table->datetime('end_date')->comment('終了日時');
            $table->boolean('is_allday')->comment('終日チェック');
            $table->string('event_color')->comment('背景色');
            $table->string('event_border_color')->comment('枠線色');
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
        Schema::dropIfExists('events');
    }
};

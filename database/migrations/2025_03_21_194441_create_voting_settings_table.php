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
        Schema::create('voting_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_election_id')->constrained()->onDelete('cascade');
            $table->boolean('allow_delegation')->default(true);
            $table->timestamp('delegation_deadline')->nullable();
            $table->integer('minimum_vote_count')->default(1);
            $table->integer('maximum_vote_count')->default(1);
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
        Schema::dropIfExists('voting_settings');
    }
};

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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_election_id')->constrained()->onDelete('cascade');
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('users')->onDelete('cascade');
            $table->enum('position_type', ['board_member', 'inspector']);
            $table->boolean('is_delegated')->default(false);
            $table->timestamps();

            // هر کاربر فقط یک بار می‌تواند به یک کاندیدا در یک انتخابات رأی دهد
            $table->unique(['group_election_id', 'voter_id', 'candidate_id', 'position_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votes');
    }
};

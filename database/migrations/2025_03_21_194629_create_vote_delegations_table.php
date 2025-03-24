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
        Schema::create('vote_delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('delegate_id')->constrained('users')->onDelete('cascade');
            $table->string('expertise_area');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();

            // هر کاربر فقط می‌تواند یک تفویض فعال در هر حوزه تخصصی داشته باشد
            $table->unique(['delegator_id', 'expertise_area', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_delegations');
    }
};

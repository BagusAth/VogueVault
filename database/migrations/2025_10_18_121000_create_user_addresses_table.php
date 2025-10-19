<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('label')->nullable();
            $table->string('receiver_name');
            $table->string('phone', 30);
            $table->string('address_line');
            $table->string('city');
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        if (Schema::hasTable('users')) {
            $users = DB::table('users')->select('id', 'name', 'phone', 'address')->get();

            foreach ($users as $user) {
                if (empty($user->address)) {
                    continue;
                }

                DB::table('user_addresses')->insert([
                    'user_id' => $user->id,
                    'label' => 'Alamat Utama',
                    'receiver_name' => $user->name,
                    'phone' => $user->phone ?? '',
                    'address_line' => $user->address,
                    'city' => '',
                    'postal_code' => null,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};

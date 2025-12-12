<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
              $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('city_id')->constrained('cities');
            $table->string('title')->default('ApartmentForRent');//عنوان العقار
            $table->text('description')->nullable();//وصف للعقار
            // $table->string('street')->nullable();
            // $table->string('district')->nullable();
            $table->string('photoOfApartment');
            $table->decimal('price', 10, 2);
            $table->string('price_unit')->default('USD');
            $table->enum('price_type',['daily','monthly','yearly']);
            $table->integer('area')->nullable();//المساحة بالمتر المربع
            $table->string('area_unit')->default('sqm');
            // $table->enum('categoryOfPropertyTpe',['apartment','house','studio','villa','penthouse','duplex'])->default('apartment');
            $table->enum('CategoryOfRentType',['family','single','students','employees'])->nullable();
            $table->integer('rooms_number')->nullable();
            // $table->integer('bathrooms_number')->nullable();
            // $table->integer('living_rooms_number')->nullable();
            $table->enum('floor',['land','first','second','third','fourth','fifth'])->nullable();
            // $table->enum('ageOfBuilding',['new','1-5 years','6-10 years','11-20 years','old'])->nullable();
            // $table->integer('street_width')->nullable();//عرض الشارع بالمتر
            // $table->string('street_width_unit')->default('m');
            // $table->enum('purpose',['residential','commercial']);//residential
            // $table->boolean('rental_status')->default(false);//الحالة مفعّل أم لا
           $table->enum('status',['pending','approved','reject'])->default('pending');
            $table->string('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};

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
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100 );  //* name = nombre del espcio solicitado / 100 caracteres
            $table->string('capacity');  //* capacity = Cuantas personas pueden albergar un espacio
            $table->text('image');
            $table->text('description'); //* Description = Descripcion del espacio 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};

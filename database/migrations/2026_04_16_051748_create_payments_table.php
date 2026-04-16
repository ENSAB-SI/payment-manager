<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->decimal('montant', 12, 2);
            $table->date('date');
            $table->enum('mode', ['virement', 'espece', 'cheque', 'carte']);
            $table->string('reference')->unique();
            $table->string('recu_path')->nullable();
            $table->text('ocr_text')->nullable();
            $table->string('nom_payeur')->nullable();
            $table->enum('statut', ['valide', 'en_attente', 'rejete'])->default('valide');
            $table->boolean('est_auto_match')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
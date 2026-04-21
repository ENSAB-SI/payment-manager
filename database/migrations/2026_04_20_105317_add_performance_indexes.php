<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index('cin');
            $table->index('code_unique');
            $table->index('statut_paiement');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->index('reference');
            $table->index('date');
        });
        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->index('reference');
            $table->index('statut');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['cin']);
            $table->dropIndex(['code_unique']);
            $table->dropIndex(['statut_paiement']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['reference']);
            $table->dropIndex(['date']);
        });
        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->dropIndex(['reference']);
            $table->dropIndex(['statut']);
        });
    }
};
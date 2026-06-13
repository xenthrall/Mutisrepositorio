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
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php no está cargado. Ejecuta [php artisan config:clear] y vuelve a intentarlo.');
        }

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('module')->nullable()->after('guard_name');
            $table->string('domain')->nullable()->after('module');
            $table->string('group')->nullable()->after('domain');
            $table->string('action')->nullable()->after('group');
            $table->string('criticality')->nullable()->after('action');
            $table->boolean('is_system')->default(false)->after('criticality');
            $table->text('description')->nullable()->after('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php no está cargado.');
        }

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropColumn([
                'module',
                'domain',
                'group',
                'action',
                'criticality',
                'is_system',
                'description',
            ]);
        });
    }
};
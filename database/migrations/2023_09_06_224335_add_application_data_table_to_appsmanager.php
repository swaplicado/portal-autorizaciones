<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicationDataTableToAppsmanager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('mysqlmngr')->table('adm_apps')->insert([
            [
                'id_app' => 2,
                'name' => 'portalautorizaciones',
                'app_url' => 'https://aeth.siieapp.com/portal-autorizaciones/public',
                'target' => '_blank',
                'description' => 'portal de autorizaciones',
                'is_active' => 1
            ],
        ]);

        DB::connection('mysqlmngr')->table('adm_permission_keys')->insert([
            [
                'key_code' => 'autorizador.requisiciones',
                'description' => 'permisos de autorizador referente a las requisiciones',
                'app_n_id' => 2
            ],
        ]);

        DB::connection('mysqlmngr')->table('adm_permission')->insert([
            [
                'id_permission' => 6,
                'app_n_id' => 2,
                'key_code' => 'autorizador.requisiciones',
                'level' => 'view',
                'description' => 'acceso a la vista requisiciones'
            ],
            [
                'id_permission' => 7,
                'app_n_id' => 2,
                'key_code' => 'autorizador.requisiciones',
                'level' => 'show',
                'description' => 'ver registros'
            ],
            [
                'id_permission' => 8,
                'app_n_id' => 2,
                'key_code' => 'autorizador.requisiciones',
                'level' => 'authorize',
                'description' => 'autorizar requisicion'
            ],
            [
                'id_permission' => 9,
                'app_n_id' => 2,
                'key_code' => 'autorizador.requisiciones',
                'level' => 'reject',
                'description' => 'rechazar requisicion'
            ],
        ]);

        DB::connection('mysqlmngr')->table('adm_roles')->insert([
            [
                'id_role' => 3,
                'role' => 'Autorizador',
                'is_super' => 0,
                'is_deleted' => 0,
                'app_n_id' => 1
            ],
        ]);

        DB::connection('mysqlmngr')->table('adm_roles_permissions')->insert([
            [
                'id' => 4,
                'app_n_id' => 2,
                'role_id' => 3,
                'permission_id' => 6
            ],
            [
                'id' => 5,
                'app_n_id' => 2,
                'role_id' => 3,
                'permission_id' => 7
            ],
            [
                'id' => 6,
                'app_n_id' => 2,
                'role_id' => 3,
                'permission_id' => 8
            ],
            [
                'id' => 7,
                'app_n_id' => 2,
                'role_id' => 3,
                'permission_id' => 9
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}

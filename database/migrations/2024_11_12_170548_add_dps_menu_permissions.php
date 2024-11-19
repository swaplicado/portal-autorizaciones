<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDpsMenuPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // agregar permisos de menu
        $menu = \App\Models\AppmanagerModels\Permission::create([
            'id_permission' => 14,
            'app_n_id' => '2',
            'key_code'=> 'autorizador.dps',
            'level' => 'view',
            'description' => 'acceso a la vista de dps',
        ]);
        $menu = \App\Models\AppmanagerModels\Permission::create([
            'id_permission' => 15,
            'app_n_id' => '2',
            'key_code'=> 'autorizador.dps',
            'level' => 'show',
            'description' => 'ver registro by id',
        ]);
        $menu = \App\Models\AppmanagerModels\Permission::create([
            'id_permission' => 16,
            'app_n_id' => '2',
            'key_code'=> 'autorizador.dps',
            'level' => 'authorize',
            'description' => 'autorizar dps',
        ]);
        $menu = \App\Models\AppmanagerModels\Permission::create([
            'id_permission' => 17,
            'app_n_id' => '2',
            'key_code'=> 'autorizador.dps',
            'level' => 'reject',
            'description' => 'rechazar dps',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Borrar los registros de la tabla permissions
        \App\Models\AppmanagerModels\Permission::whereIn('id_permission', [14, 15, 16, 17])->delete();
    }
}

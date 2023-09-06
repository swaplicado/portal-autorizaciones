<?php 
namespace App\Constants;

class SysConst {
    /**
     * Constantes de la tabla adm_typesuser
     */
    public const TYPE_SUPER = 1;
    public const TYPE_MANAGER = 2;
    public const TYPE_ESTANDAR = 3;

    /**
     * Constantes de la tabla adm_rol
     */
    public const ROL_ADMIN = 1;
    public const ROL_PROVEEDOR = 2;
    public const ROL_AUTORIZADOR = 3;

    /**
     * Constantes de estatus de requisiciones de siie
     */
    public const AUTH_STATUS_NA = 1;
    public const AUTH_STATUS_PENDING = 2;
    public const AUTH_STATUS_IN_PROCESS = 3;
    public const AUTH_STATUS_AUTHORIZED = 4;
    public const AUTH_STATUS_REJECTED = 5;

    public const lAuthStatus = [
        ['id' => 1, 'text' => 'No aplica'],
        ['id' => 2, 'text' => 'Pendiente'],
        ['id' => 3, 'text' => 'En proceso'],
        ['id' => 4, 'text' => 'Autorizado'],
        ['id' => 5, 'text' => 'Rechazado'],
    ];

    public const lTypes = [
        ['id' => 1, 'text' => 'Requisicion'],
        ['id' => 2, 'text' => 'Documento'],
    ];
}
?>
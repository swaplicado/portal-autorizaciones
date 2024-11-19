<?php

namespace App\Models\AppmanagerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionKey extends Model
{
    use HasFactory;

    protected $connection= 'mysqlmngr';
    protected $primaryKey = 'key_code';
    protected $table = 'adm_permission_keys';
    public $timestamps = false;

    protected $fillable = [
        'key_code',
        'description',
        'app_n_id'
    ];
}

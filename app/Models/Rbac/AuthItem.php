<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
name            String      (PK)
type            Type[role, operation]
data            String
description     String
created_at      Timestamp
updated_at      Timestamp
======================================= */


class AuthItem extends Model
{
    // use HasFactory;

    protected $guarded = ['created_at'];

    protected $connection = 'pgsql';

    protected $table = 'public.auth_item';

    protected $primaryKey = 'name';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const TYPE_ROLE = 'role';
    const TYPE_OPERATION = 'operation';

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function authAssignments()
    {
        return $this->hasMany(AuthAssignment::class, 'item_name', 'name');
    }

    public function authItemParents()
    {
        return $this->hasMany(AuthItemChild::class, 'parent', 'name');
    }

    public function authItemChilds()
    {
        return $this->hasMany(AuthItemChild::class, 'child', 'name');
    }

    public function getCreatedAtAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function attributes()
    {
        return [
            'name' => 'Item Name'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => ':attribute tidak boleh kosong'
        ];
    }
}

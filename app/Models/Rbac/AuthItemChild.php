<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
parent           String     (PK)
child            String     (PK)
======================================= */


class AuthItemChild extends Model
{
    // use HasFactory;

    // protected $guarded = [];

    protected $connection = 'pgsql';

    protected $table = 'public.auth_item_child';

    protected $primaryKey = ['parent', 'child'];

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function authItemParent()
    {
        return $this->belongsTo(AuthItem::class, 'parent', 'name');
    }

    public function authItemChild()
    {
        return $this->belongsTo(AuthItem::class, 'child', 'name');
    }

    public function attributes()
    {
        return [
            'parent' => 'Parent',
            'child' => 'Child'
        ];
    }

    public function messages()
    {
        return [
            'parent.required' => ':attribute tidak boleh kosong',
            'child.required' => ':attribute tidak boleh kosong'
        ];
    }
}

<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

/* ======================================
Attributes :
item_name       String      (PK)    (FK) => public.auth_item
user_id         Int         (PK)    (FK) => public.users
status          Type[active, inactive]
created_at      Timestamp
======================================= */


class AuthAssignment extends Model
{
    // use HasFactory;

    protected $guarded = ['created_at'];

    protected $connection = 'pgsql';

    protected $table = 'public.auth_assignment';

    protected $primaryKey = ['item_name', 'user_id'];

    // protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function authItem()
    {
        return $this->belongsTo(AuthItem::class, 'item_name', 'name');
    }

    public function getCreatedAtAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function attributes()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID'
        ];
    }

    public function messages()
    {
        return [
            'item_name.required' => ':attribute tidak boleh kosong',
            'user_id.required' => ':attribute tidak boleh kosong'
        ];
    }
}

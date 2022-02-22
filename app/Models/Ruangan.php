<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
ruangan_id      Int         (PK)
isenabled       Boolean
nama_ruangan    String
cre_user        String
cre_date        Timestamp
upd_user        String
upd_date        Timestamp
======================================= */

class Ruangan extends Model
{
    // use HasFactory;
    protected $guarded = ['ruangan_id', 'cre_date'];

    protected $connection = 'pgsql';

    protected $table = 'public.m_ruangan';

    protected $primaryKey = 'ruangan_id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'cre_date';
    const UPDATED_AT = 'upd_date';

    // https://laravel.com/docs/8.x/eloquent-relationships#one-to-many
    public function locations()
    {
        // return $this->hasMany(PerawatanItem::class, 'foreign_key', 'local_key/primary_key');
        return $this->hasMany(Lokasi::class, 'ruangan_id', 'ruangan_id');
    }

    public function careItems()
    {
        return $this->hasMany(PerawatanItem::class, 'ruangan_id', 'ruangan_id');
    }

    public function getCreDateAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function getUpdDateAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }
}

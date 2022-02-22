<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
kelompok_id     Int         (PK)
isenabled       Boolean
nama_kelompok   String
cre_user        String
cre_date        Timestamp
upd_user        String
upd_date        Timestamp
======================================= */

class KelompokAset extends Model
{
    // use HasFactory;

    protected $guarded = ['kelompok_id', 'cre_date'];

    protected $connection = 'pgsql';

    protected $table = 'public.m_kelompok_aset';

    protected $primaryKey = 'kelompok_id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'cre_date';
    const UPDATED_AT = 'upd_date';

    public function products()
    {
        return $this->hasMany(Produk::class, 'kelompok_id', 'kelompok_id');
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

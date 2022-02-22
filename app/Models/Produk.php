<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
produk_id       Int             (PK)
isenabled       Boolean
kelompok_id     Int             (FK) => public.m_kelompok_aset
kodeproduk      String
kodebmn         String
namaproduk      String
merk            String
tipe            String
no_sn           String
satuan_id       Int             (FK) => public.m_satuan
hargajual       Float
hargabeli       Float
cre_user        String
cre_date        Timestamp
upd_user        String
upd_date        Timestamp
======================================= */

class Produk extends Model
{
    // use HasFactory;

    protected $guarded = ['produk_id', 'cre_date'];

    protected $connection = 'pgsql';

    protected $table = 'public.m_produk';

    protected $primaryKey = 'produk_id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'cre_date';
    const UPDATED_AT = 'upd_date';

    protected $casts = [
        'cre_date' => 'datetime'
    ];

    public function kelompok()
    {
        return $this->belongsTo(KelompokAset::class, 'kelompok_id', 'kelompok_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id', 'satuan_id');
    }

    public function careItems()
    {
        return $this->hasMany(PerawatanItem::class, 'produk_id', 'produk_id');
    }

    public function productAttributes()
    {
        return $this->hasMany(ProdukAtribut::class, 'produk_id', 'produk_id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
perawatan_item_id   String      (PK)
isenabled           Boolean
perawatan_id        Int         (FK) => public.t_perawatan
produk_id           Int         (FK) => public.m_produk
ruangan_id          Int         (FK) => public.m_ruangan
lokasi_id           Int         (X) => public.m_lokasi [Not Related] => Not Required
cre_user            String
cre_date            Timestamp
upd_user            String
upd_date            Timestamp
teknisi_id          Int         (X) => public.users [Not Related]
status              Type[Open, Cancel]
======================================= */

class PerawatanItem extends Model
{
    // use HasFactory;

    protected $guarded = ['perawatan_item_id', 'cre_date'];

    protected $connection = 'pgsql';

    protected $table = 'public.t_perawatan_item';

    protected $primaryKey = 'perawatan_item_id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    const CREATED_AT = 'cre_date';
    const UPDATED_AT = 'upd_date';

    const STATUS_OPEN = 'Open';
    const STATUS_CANCEL = 'Cancel';

    public function perawatan()
    {
        return $this->belongsTo(Perawatan::class, 'perawatan_id', 'perawatan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    // https://laravel.com/docs/8.x/eloquent-relationships#one-to-one-defining-the-inverse-of-the-relationship
    public function ruangan()
    {
        // return $this->belongsTo(Ruangan::class, 'foreign_key', 'owner_key/primary_key');
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'ruangan_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'lokasi_id');
    }

    // Not related
    // walau tanpa relasi antar tabel, tapi penggunaan relasi di model Laravel berfungsi dengan baik
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id', 'id');
    }

    public function getCreDateAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function getUpdDateAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function generatePrimaryKey()
    {
        $this->perawatan_item_id = 'PI'.date('YmdHis').''.rand(100,999);
    }
}

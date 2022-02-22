<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
perawatan_id    Int         (PK)
isenabled       Boolean
no_trans        String
tgl_trans       Timestamp
keterangan      String
cre_user        String
cre_date        Timestamp
upd_user        String
upd_date        Timestamp
======================================= */

class Perawatan extends Model
{
    // use HasFactory;

    protected $guarded = ['cre_date'];

    protected $connection = 'pgsql';

    protected $table = 'public.t_perawatan';

    protected $primaryKey = 'perawatan_id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    const CREATED_AT = 'cre_date';
    const UPDATED_AT = 'upd_date';

    public function itemPerawatan()
    {
        return $this->hasMany(PerawatanItem::class, 'perawatan_id', 'perawatan_id');
    }

    public function getTglTransAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
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
        $this->perawatan_id = 'PM'.date('YmdHis').''.rand(100,999);
    }
}

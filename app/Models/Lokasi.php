<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
lokasi_id       Int         (PK)
ruangan_id      Int         (X) => public.m_ruangan [Not Related]
isenabled       Boolean
nama_lokasi     String
cre_user        String
cre_date        Timestamp
upd_user        String
upd_date        Timestamp
======================================= */


class Lokasi extends Model
{
    // use HasFactory;

    protected $guarded = ['lokasi_id', 'cre_date'];

    protected $connection = 'pgsql';

    protected $table = 'public.m_lokasi';

    protected $primaryKey = 'lokasi_id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'cre_date';
    const UPDATED_AT = 'upd_date';

    // https://laravel.com/docs/8.x/eloquent-relationships#one-to-one-defining-the-inverse-of-the-relationship
    // Relasi Aneh
    public function ruangan()
    {
        // return $this->belongsTo(Ruangan::class, 'foreign_key', 'owner_key/primary_key');
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'ruangan_id');

        // return $this->hasOne(Phone::class, 'foreign_key', 'local_key/primary_key');
        // return $this->hasOne(Ruangan::class, 'ruangan_id', 'ruangan_id');
    }

    // https://laravel.com/docs/8.x/eloquent-relationships#one-to-many
    public function careItems()
    {
        // return $this->hasMany(PerawatanItem::class, 'foreign_key', 'local_key/primary_key');
        return $this->hasMany(PerawatanItem::class, 'lokasi_id', 'lokasi_id');
    }

    // https://laravel.com/docs/8.x/eloquent-mutators
    public function getCreDateAttribute($value)
    {
        // https://stackoverflow.com/q/8405087
        // What is datetime format 2022-02-01T12:39:17.110782Z ?
        // Answer : ISO8601

        // return '2022-02-03 00:46:00';
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function getUpdDateAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function attributes()
    {
        return [
            'ruangan_id' => 'ID Ruangan'
        ];
    }

    public function messages()
    {
        return [
            'ruangan_id.required' => ':attribute tidak boleh kosong'
        ];
    }
}

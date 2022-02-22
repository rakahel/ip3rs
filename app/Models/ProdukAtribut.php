<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* ======================================
Attributes :
produk_atribut_id   Int             (PK)
nama_atribut        String
produk_id           Int             (FK) => m_produk
kategori            Type
cre_user            String
cre_date            Timestamp
upd_user            String
upd_date            Timestamp
answer              String
======================================= */

class ProdukAtribut extends Model
{
    // use HasFactory;

    protected $guarded = ['produk_atribut_id', 'cre_date'];

    protected $connection = 'pgsql';

    protected $table = 'public.m_produk_atribut';

    protected $primaryKey = 'produk_atribut_id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = true;

    const CREATED_AT = 'cre_date';
    const UPDATED_AT = 'upd_date';

    const KATEGORI_PARAMETER = 'Parameter';
    const KATEGORI_BAGIAN_ALAT = 'Bagian_Alat';

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function getCreDateAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function getUpdDateAttribute($value)
    {
        return empty($value) ? null : date('Y-m-d H:i:s', strtotime($value));
    }

    public function getFormatAnswer()
    {
        return [
            self::KATEGORI_PARAMETER => [
                'kebutuhan_alat' => [
                    'satuan' => '',
                    'value' => ''
                ],
                'terukur' => [
                    'satuan' => '',
                    'value' => ''
                ]
            ],
            self::KATEGORI_BAGIAN_ALAT => [
                'pemeriksaan_fisik' => [
                    'satuan' => 'Boolean',
                    'value' => true
                ],
                'pemeriksaan_fungsi' => [
                    'satuan' => 'Boolean',
                    'value' => true
                ]
            ]
        ];
    }

    public function setAnswer(String $kategori):void
    {
        $format = $this->getFormatAnswer();
        $this->answer = json_encode($format[$kategori]);
    }

    public function formatIsValid(String $kategori, $structure):Bool
    {
        try {
            // print('<pre>'.print_r([$kategori, $structure],true).'</pre>');exit;
            $format = $this->getFormatAnswer();
             // print('<pre>'.print_r($format[$kategori],true).'</pre>');exit;
            foreach($format[$kategori] as $category => $rows) {
                // print('<pre>'.print_r($structure[$category],true).'</pre>');exit;
                if(!isset($structure[$category])) {
                    throw new \Exception('Invalid! [#1]');
                }

                foreach($rows as $key => $value) {
                    if(!array_key_exists($key, $structure[$category])) {
                        throw new \Exception('Invalid! [#2] => '.json_encode($structure[$category][$key]));
                    }

                    if($kategori==self::KATEGORI_BAGIAN_ALAT) {
                        if($key=="satuan") {
                            if($structure[$category][$key]!='Boolean') {
                                throw new \Exception('Nilai "satuan" untuk '.self::KATEGORI_BAGIAN_ALAT.' harus "Boolean"');
                            }
                        } elseif($key=="value") {
                            if(!is_bool($structure[$category][$key])) {
                                throw new \Exception('Nilai harus berupa Boolean[true,false]');
                            }
                        }
                    }
                }
            }
            return true;
        } catch(\Exception $e) {
            return false;
            //throw $e;
        }
    }
}

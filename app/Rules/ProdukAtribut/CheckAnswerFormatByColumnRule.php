<?php

namespace App\Rules\ProdukAtribut;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\ProdukAtribut;

class CheckAnswerFormatByColumnRule implements Rule
{
    private $_request;
    protected $message = null;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // print('<pre>'.print_r([$this->_request->route('id'),$value],true).'</pre>');exit;
        $id = $this->_request->route('id');
        $column = $this->_request->route('column');
        $model = ProdukAtribut::where((new ProdukAtribut)->getKeyName(), $id)->first();
        if(!$model) {
            $this->setMessage('Data tidak ditemukan');
            return false;
        }

        //print('<pre>'.print_r($model,true).'</pre>');exit;
        if($model->kategori==ProdukAtribut::KATEGORI_PARAMETER) {
            return $this->checkFormat($model->kategori, $column, $value);
        } elseif($model->kategori==ProdukAtribut::KATEGORI_BAGIAN_ALAT) {
            return $this->checkFormat($model->kategori, $column, $value);
        } else {
            $this->setMessage('Validator ini mungkin sudah kadaluwarsa! Hanya dapat menangani tipe: '.implode(',',[ProdukAtribut::KATEGORI_PARAMETER, ProdukAtribut::KATEGORI_BAGIAN_ALAT]));
            return false;
        }
    }

    protected function checkFormat($kategori, $column, $format_column):Bool
    {
        try {
            $format = (new ProdukAtribut)->getFormatAnswer();
            if(!isset($format[$kategori][$column])) {
                throw new \Exception('Invalid! [#1]');
            }

            foreach($format[$kategori][$column] as $key => $value) {
                if(!array_key_exists($key, $format_column)) {
                    throw new \Exception('Invalid! [#2]');
                }

                //print('<pre>'.print_r($kategori,true).'</pre>');exit;
                if($kategori==ProdukAtribut::KATEGORI_BAGIAN_ALAT) {
                    //print('<pre>'.print_r($key,true).'</pre>');exit;
                    if($key=="satuan") {
                        if($format_column[$key]!='Boolean') {
                            throw new \Exception('Nilai "satuan" untuk '.ProdukAtribut::KATEGORI_BAGIAN_ALAT.' harus "Boolean"');
                        }
                    } elseif($key=="value") {
                        if(!is_bool($format_column[$key])) {
                            throw new \Exception('Nilai harus berupa Boolean[true,false]');
                        }
                    }
                }
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message==null ? 'Format jawaban tidak valid! Cek format jawaban disini GET:/api/product/attribute/format/{kategori}' : $this->message;
    }

    public function setMessage($message):void
    {
        $this->message = $message;
    }
}

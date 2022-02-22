<?php

namespace App\Rules\ProdukAtribut;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\ProdukAtribut;

class CheckAnswerFormatByRowRule implements Rule
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
        $model = ProdukAtribut::where((new ProdukAtribut)->getKeyName(), $id)->first();
        if(!$model) {
            $this->setMessage('Data tidak ditemukan');
            return false;
        }

        //print('<pre>'.print_r($model,true).'</pre>');exit;
        if($model->kategori==ProdukAtribut::KATEGORI_PARAMETER) {
            return (new ProdukAtribut)->formatIsValid($model->kategori, $value);
        } elseif($model->kategori==ProdukAtribut::KATEGORI_BAGIAN_ALAT) {
            return (new ProdukAtribut)->formatIsValid($model->kategori, $value);
        } else {
            $this->setMessage('Validator ini mungkin sudah kadaluwarsa! Hanya dapat menangani tipe: '.implode(',',[ProdukAtribut::KATEGORI_PARAMETER, ProdukAtribut::KATEGORI_BAGIAN_ALAT]));
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

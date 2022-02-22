<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Database\Query\Expression;

class LocationController extends Controller
{
    public function __construct()
    {
        session()->put('cilog.session_id', date('YmdHis').rand(1000,9999));
    }

    public function search(Request $request)
    {
        $params = ['search', 'value'];
        $fields = ['nama_lokasi'];
        $withCondition = true;
        for($i=0; $i<sizeof($params); $i++) {
            if(!array_key_exists($params[$i], $request->all())) {
                $withCondition = false;
                break;
            }

            if($params[$i]=='search' && !in_array($request->get($params[$i]),$fields)) {
                $result = [
                    'code' => 'F0001',
                    'noref' => session()->get('cilog.session_id'),
                    'type' => 'error',
                    'message' => "Paramater 'search' tidak valid! Coba pilih salah satu : [".implode(',',$fields)."]"
                ];
                cilog()->toDb(json_encode($result));
                return response()->json($result, 400);
                // $withCondition = false;
                // break;
            } elseif($params[$i]=='value' && empty(trim($request->get($params[$i])))) {
                $withCondition = false;
                break;
            }
        }

        if($withCondition) {
            $model = Lokasi::where(new Expression('lower('.$request->get('search').')'), 'like', '%'.strtolower($request->get('value')).'%')->orderBy($request->get('search'), 'asc')->get();
        } else {
            $model = Lokasi::orderBy('nama_lokasi', 'asc')->get();
        }

        if($model->isEmpty()) {
            $result = [
                'code' => 'R0001',
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => 'Data not found'
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, 404);
        }

        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'Success',
            'data' => $model
        ];
        cilog()->toDb(json_encode($result));
        return response()->json($result, 200);
    }
}

?>

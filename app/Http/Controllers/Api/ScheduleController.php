<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Expression;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Models\Perawatan;
use App\Models\PerawatanItem;

class ScheduleController extends Controller
{
    public function __construct()
    {
        session()->put('cilog.session_id', date('YmdHis').rand(1000,9999));
    }

    public function search(Request $request)
    {
        $params = ['search', 'value'];
        $fields = ['no_trans', 'tgl_trans', 'keterangan'];
        $item_fields = ['status'];
        $product_fields = ['namaproduk', 'merk'];
        $withCondition = true;
        $field_merge = array_merge($fields, $item_fields, $product_fields);
        // return response()->json($field_merge,200);
        for($i=0; $i<sizeof($params); $i++) {
            if(!array_key_exists($params[$i], $request->all())) {
                $withCondition = false;
                break;
            }

            if($params[$i]=='search' && !in_array($request->get($params[$i]),$field_merge)) {
                $result = [
                    'code' => 'F0001',
                    'noref' => session()->get('cilog.session_id'),
                    'type' => 'error',
                    'message' => "Paramater 'search' tidak valid! Coba pilih salah satu : [".implode(',',$field_merge)."]"
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
            $model = Perawatan::with([
                'itemPerawatan' => function($query) use ($request, $item_fields, $product_fields) {
                    $query->with([
                        'produk' => function($query) use ($request, $product_fields) {
                            $query->select(['produk_id', 'namaproduk', 'merk']);

                            if(in_array($request->get('search'),$product_fields)) {
                                $value = trim($request->get('value'));
                                if(!empty($value)) {
                                    $query->where(new Expression('lower('.$request->get('search').')'), 'like', '%'.strtolower($request->get('value')).'%');
                                }
                            }

                            $model = $query->get();
                            if($model->isEmpty()) {
                                header('Content-Type: application/json');
                                $result = [
                                    'code' => 'R0001',
                                    'noref' => session()->get('cilog.session_id'),
                                    'type' => 'error',
                                    'message' => 'Data not found'
                                ];
                                cilog()->toDb(json_encode($result));
                                echo json_encode($result); exit;
                            }

                        },
                        'teknisi' => function($query) {
                            $query->select(['id', 'name', 'email']);
                        }
                    ]);

                    if(in_array($request->get('search'),$item_fields)) {
                        $value = trim($request->get('value'));
                        if($request->get('search')=='status' && !empty($value)) {
                            $status = [PerawatanItem::STATUS_OPEN, PerawatanItem::STATUS_CANCEL];
                            if(in_array($value,$status)) {
                                $query->where($request->get('search'), $value);
                            } else {
                                header('Content-Type: application/json');
                                $result = [
                                    'code' => 'F0001',
                                    'noref' => session()->get('cilog.session_id'),
                                    'type' => 'error',
                                    'message' => "Paramater 'value' tidak valid! Coba pilih salah satu : [".implode(',',$status)."]"
                                ];
                                cilog()->toDb(json_encode($result));
                                echo json_encode($result); exit;
                            }
                        }
                    }

                    $model = $query->get();
                    if($model->isEmpty()) {
                        header('Content-Type: application/json');
                        $result = [
                            'code' => 'R0001',
                            'noref' => session()->get('cilog.session_id'),
                            'type' => 'error',
                            'message' => 'Data not found'
                        ];
                        cilog()->toDb(json_encode($result));
                        echo json_encode($result); exit;
                    }
                }
            ]);

            // die('GATE #1');
            if(in_array($request->get('search'),$fields)) {
                $model->where(new Expression('lower('.$request->get('search').')'), 'like', '%'.strtolower($request->get('value')).'%');
            }

            $model->orderBy('no_trans', 'asc');
            $model = $model->get();
            // return response()->json($model,200);

        } else {
            $model = Perawatan::with([
                'itemPerawatan' => function($query) {
                    $query->with([
                        'produk' => function($query) {
                            $query->select(['produk_id', 'namaproduk', 'merk']);
                        },
                        'teknisi' => function($query) {
                            $query->select(['id', 'name', 'email']);
                        }
                    ]);
                }
            ])->orderBy('no_trans', 'asc')->get();
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

        $data = [];
        foreach($model->toArray() as $index => $row) {
            $data[$index]['perawatan_id'] = $row['perawatan_id'];
            $data[$index]['no_trans'] = $row['no_trans'];
            $data[$index]['tgl_trans'] = date('Y-m-d',strtotime($row['tgl_trans']));
            $data[$index]['keterangan'] = $row['keterangan'];

            $data[$index]['item_perawatan'] = [];
            if(empty($row['item_perawatan'])==false) {
                foreach($row['item_perawatan'] as $item) {
                    $data[$index]['item_perawatan'][] = [
                        'perawatan_item_id' => $item['perawatan_item_id'],
                        'status' => $item['status'],
                        'produk' => $item['produk'],
                        'teknisi' => $item['teknisi']
                    ];
                }
            }
        }

        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'Success',
            'data' => $data
        ];
        cilog()->toDb(json_encode($result));
        return response()->json($result, 200);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $log_code = 'xxxxx'; $http_code = 0; $gate = 0;
        try {
            $validator = \Validator::make($request->post(), [
                'tgl_trans' => ['required', 'date_format:Y-m-d'],
                'produk_id' => ['required', 'numeric', 'exists:m_produk,produk_id'],
                'ruangan_id' => ['required', 'numeric', 'exists:m_ruangan,ruangan_id'],
                'lokasi_id' => ['nullable', 'numeric', 'exists:m_lokasi,lokasi_id'],
                'teknisi_id' => ['required', 'numeric', 'exists:users,id'],
            ], [
                'produk_id.required' => ':attribute tidak boleh kosong! Anda dapat memperoleh nilai ini dari "GET:/api/product"',
                'produk_id.exists' => ':attribute tidak valid! Anda dapat memperoleh nilai ini dari "GET:/api/product"',

                'ruangan_id.required' => ':attribute tidak boleh kosong! Anda dapat memperoleh nilai ini dari "GET:/api/room"',
                'ruangan_id.exists' => ':attribute tidak valid! Anda dapat memperoleh nilai ini dari "GET:/api/room"',

                'lokasi_id.exists' => ':attribute tidak valid! Anda dapat memperoleh nilai ini dari "GET:/api/location"',
            ]);
            if($validator->fails()) {
                $log_code = 'F0001'; $http_code = 400; $gate = 1;
                DB::rollBack();
                $result = [
                    'code' => $log_code,
                    'noref' => session()->get('cilog.session_id'),
                    'type' => 'error',
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->messages()->get('*')
                ];
                cilog()->toDb(json_encode($result));
                return response()->json($result, $http_code);
            }

            $created_by = 'Admin';
            /* $perawatan = Perawatan::where('no_trans', 'xxx')->first();
            if(!$perawatan) {
                $perawatan = new Perawatan();
                $perawatan->generatePrimaryKey();
                $perawatan->isenabled = true;
                $perawatan->no_trans = $perawatan->perawatan_id;
                $perawatan->tgl_trans = $request->post('tgl_trans');
                $perawatan->keterangan = null;
                $perawatan->cre_user = $created_by;
                if(!$perawatan->save()) {
                    $log_code = 'F0002'; $http_code = 500; $gate = 2;
                    throw new \Exception('Failed to save data',$http_code);
                }
            } */

            $perawatan = new Perawatan();
            $perawatan->generatePrimaryKey();
            $perawatan->isenabled = true;
            $perawatan->no_trans = $perawatan->perawatan_id;
            $perawatan->tgl_trans = $request->post('tgl_trans');
            $perawatan->keterangan = null;
            $perawatan->cre_user = $created_by;
            if(!$perawatan->save()) {
                $log_code = 'F0002'; $http_code = 500; $gate = 2;
                throw new \Exception('Failed to save data',$http_code);
            }

            //die('GATE #1');
            //die($perawatan->perawatan_id);

            $perawatan_item = new PerawatanItem();
            $perawatan_item->generatePrimaryKey();
            $perawatan_item->isenabled = true;
            $perawatan_item->perawatan_id = $perawatan->perawatan_id;
            $perawatan_item->produk_id = $request->post('produk_id');
            $perawatan_item->ruangan_id = $request->post('ruangan_id');
            $perawatan_item->lokasi_id = $request->post('lokasi_id');
            $perawatan_item->teknisi_id = $request->post('teknisi_id');
            $perawatan_item->cre_user = $created_by;
            $perawatan_item->status = PerawatanItem::STATUS_OPEN;
            if(!$perawatan_item->save()) {
                $log_code = 'F0002'; $http_code = 500; $gate = 3;
                throw new \Exception('Failed to save data',$http_code);
            }

            // die('GATE #2');

            $log_code = '00000'; $http_code = 200; $gate = 4;
            DB::commit();
            $result = [
                'code' => $log_code,
                'noref' => session()->get('cilog.session_id'),
                'type' => 'success',
                'message' => 'Data saved successfully'
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, $http_code);

        } catch(\Exception $e) {
            DB::rollBack();
            // die($e->getMessage());
            $result = [
                'code' => $log_code,
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => $e->getMessage()." [Gate:{$gate}]"
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, $http_code);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $validator = \Validator::make($request->post(), [
            'status' => ['required', Rule::in([PerawatanItem::STATUS_OPEN, PerawatanItem::STATUS_CANCEL])]
        ], [
            'status.in' => 'Nilai :attribute tidak valid! Pilih salah satu: ['.implode(',',[PerawatanItem::STATUS_OPEN, PerawatanItem::STATUS_CANCEL]).']'
        ]);
        if($validator->fails()) {
            $result = [
                'code' => 'F0001',
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages()->get('*')
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, 400);
        }

        $model = PerawatanItem::where((new PerawatanItem)->getKeyName(), $id)->first();
        if(!$model) {
            $result = [
                'code' => 'R0001',
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => 'Data not found'
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, 404);
        }

        $model->status = $request->post('status');
        $model->upd_user = 'Admin';
        if(!$model->save()) {
            $result = [
                'code' => 'F0003',
                'noref' => session()->get('cilog.session_id'),
                'type' => 'error',
                'message' => 'Failed to update data'
            ];
            cilog()->toDb(json_encode($result));
            return response()->json($result, 500);
        }

        $result = [
            'code' => '00000',
            'noref' => session()->get('cilog.session_id'),
            'type' => 'success',
            'message' => 'Data changed successfully'
        ];
        cilog()->toDb(json_encode($result));
        return response()->json($result, 200);
    }
}

?>

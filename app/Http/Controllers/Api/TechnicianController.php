<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Expression;

use App\Models\Rbac\AuthItem;
use App\Models\Rbac\AuthAssignment;
use App\Models\User;

class TechnicianController extends Controller
{
    public function __construct()
    {
        session()->put('cilog.session_id', date('YmdHis').rand(1000,9999));
    }

    public function index()
    {
    }

    public function search(Request $request)
    {
        $params = ['search', 'value'];
        $fields = ['name', 'email'];
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

        /*SELECT a.user_id FROM auth_assignment AS a
        INNER JOIN auth_item AS b
            ON a.item_name = b.name
                AND b.type = 'role'
                AND b.name = 'technician';
        SELECT * FROM users WHERE id IN(2,3,4,5,6);*/
        $ids = AuthAssignment::from('auth_assignment AS a')
        ->select('a.user_id')
        ->join('auth_item AS b', function($join) {
            $join->on('a.item_name', '=', 'b.name')
            ->on('b.type', '=', \DB::raw("'".AuthItem::TYPE_ROLE."'"))
            ->on('b.name', '=', \DB::raw("'technician'"));
        })->get();
        // $ids = array_column($ids->toArray(),'user_id');
        $ids = $ids->pluck('user_id')->toArray();
        $model = User::whereIn('id', $ids);

        if($withCondition) {
            $model->where(new Expression('lower('.$request->get('search').')'), 'LIKE', '%'.strtolower($request->get('value')).'%');
        }

        $model = $model->orderBy('name', 'asc')
        ->get(['id', 'name', 'email']);
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

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}

?>

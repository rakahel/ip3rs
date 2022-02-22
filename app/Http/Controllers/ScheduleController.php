<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function create($product_id)
    {
        return view('schedule.create');
    }
}

?>

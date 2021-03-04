<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class YearController extends Controller
{

    public function view(){


        $data['allData']=Year::all();


        return view ('backend.setup.year.view-year',$data);


    }



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModelName;

class ModelNameController extends Controller
{
    public function index()
    {
        $items = ModelName::paginate();
        return view('modelname.index', compact('items'));
    }
}
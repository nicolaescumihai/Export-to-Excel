<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AllPropertiesExport;
use Maatwebsite\Excel\Facades\Excel;

class AllPropertiesController extends Controller
{
    // public function export() 
    // {
    //     return Excel::download(new AllPropertiesExport, 'All properties.xlsx');
    // }

    public function storeExcel() 
    {
    
     return Excel::download(new AllPropertiesExport, 'All properties.xlsx');
    }


}

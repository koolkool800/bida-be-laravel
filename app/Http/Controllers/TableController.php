<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\SettingTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function create(Request $request)
    {
        $is_exist_setting_table_id = SettingTable::where('id', $request->input("setting_table_id"))->first();
        if(!$is_exist_setting_table_id) {
            return response()->json(
                [
                    'error_code' =>  'SETTING_TABLE_001', 
                    'error' => 'Unauthorized', 
                    'message' => 'Setting table not found'
                ], 400); 
        }

        $insertTable = [
            "name" => $request->input("name"),
            "is_available" => true,
            "setting_table_id" => $request->input("setting_table_id")
        ];

        $newTable = Table::create($insertTable);

        return response()->json([
            'message' => 'Successfully',
            'data' => $newTable
        ]);
    }
}

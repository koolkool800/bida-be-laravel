<?php

namespace App\Http\Controllers;

use App\Enums\Error\SettingTableErrorCode;
use App\Enums\Error\TableErrorCode;
use App\Models\Table;
use App\Models\SettingTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function create(Request $request)
    {
        $name = $request->input("name");
        $setting_table_id = $request->input("setting_table_id");
        // TODO: validation

        $is_exist_setting_table_id = SettingTable::where('id', $setting_table_id)->first();
        if(!$is_exist_setting_table_id) {
            return response()->json(
                [
                    'error_code' =>  SettingTableErrorCode::SETTING_TABLE_NOT_FOUND, 
                    'message' => 'Setting table not found'
                ], 400); 
        }

        $is_exist_table_name = Table::where('name', $name)->first();
        if($is_exist_table_name) {
            return response()->json(
                [
                    'error_code' =>  TableErrorCode::TABLE_ALREADY_EXIST, 
                    'message' => 'Table name already exist'
                ], 400); 
        }

        $insertTable = [
            "name" => $name,
            "is_available" => true,
            "setting_table_id" => $setting_table_id
        ];
        $newTable = Table::create($insertTable);
        
        return response()->json([
            'message' => 'Successfully',
            'data' => $newTable
        ]);
    }
}

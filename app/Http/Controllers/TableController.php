<?php

namespace App\Http\Controllers;

use App\Enums\Error\SettingTableErrorCode;
use App\Enums\Error\TableErrorCode;
use App\Models\Table;
use App\Models\SettingTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function delete(Request $request, $id) {
        $table = Table::where('id', $id)->first();
        if(!$table) {
            return response()->json(
                [
                    'error_code' =>  TableErrorCode::TABLE_NOT_FOUND, 
                    'message' => 'Table not found'
                ], 400); 
        }

        $table->delete();
        
        return response()->json([
            'message' => 'Successfully',
            'data' => 1
        ]);

    }

    public function find_many(Request $request) {
        $pageIndex = $request->input('pageIndex', 1); 
        $pageSize = $request->input('pageSize', 10);  
        $is_available = $request->input('is_available', null);
        $q = $request->input('q', null);

        $query = DB::table('tables');
        if($is_available) {
            $is_available = ($is_available === 'false') ? false : true;
            $query->where('tables.is_available', $is_available);
        }
        if($q) {
            $query->where('tables.name', 'LIKE', '%' . $q . '%');
        }
        $table_list = $query->join('setting_table', 'tables.setting_table_id', '=', 'setting_table.id')
            ->select('tables.*', 'setting_table.price', 'setting_table.type')
            ->paginate($pageSize, ['*'], 'page', $pageIndex);
     
        return response()->json([
            'message' => 'Successfully',
            'data' => $table_list
        ]);
    }
}

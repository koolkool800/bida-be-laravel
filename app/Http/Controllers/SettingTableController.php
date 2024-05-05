<?php

namespace App\Http\Controllers;

use App\Enums\Error\SettingTableErrorCode;
use App\Models\SettingTable;
use Illuminate\Http\Request;

class SettingTableController extends Controller
{
    public function create(Request $request)
    {
        $type = $request->input("type");
        $price = $request->input("price");
        // TODO: validating
 
        $is_exist_setting_table_type = SettingTable::where('type', $type)->first();
        if($is_exist_setting_table_type) {
            return response()->json(
                [
                    'error_code' =>  SettingTableErrorCode::SETTING_TABLE_ALREADY_EXIST, 
                    'message' => 'Setting table already exist'
                ], 400); 
        }

        $insertSettingTable = [
            "type" => $type,
            "price" => $price
        ];
        $new_setting_table = SettingTable::create($insertSettingTable);

        return response()->json([
            'message' => 'Successfully',
            'data' => $new_setting_table
        ]);

    }

    public function update(Request $request)
    {
        // $id 
        // $type = $request->input("type");
        // $price = $request->input("price");
        // // TODO: validating

        // $is_exist_setting_table_type = SettingTable::where('type', $type)->first();
        // if(!$is_exist_setting_table_type) {
        //     return response()->json(
        //         [
        //             'error_code' =>  SettingTableErrorCode::SETTING_TABLE_ALREADY_EXIST, 
        //             'message' => 'Setting table already exist'
        //         ], 400); 
        // }

        // $insertSettingTable = [
        //     "type" => $type,
        //     "price" => $price
        // ];
        // $new_setting_table = SettingTable::create($insertSettingTable);

        // return response()->json([
        //     'message' => 'Successfully',
        //     'data' => $new_setting_table
        // ]);
    }
}

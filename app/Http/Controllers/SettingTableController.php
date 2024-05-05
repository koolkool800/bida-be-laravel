<?php

namespace App\Http\Controllers;

use App\Enums\Error\SettingTableErrorCode;
use App\Models\SettingTable;
use Illuminate\Database\UniqueConstraintViolationException;
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

    public function update(Request $request, $id)
    {
        try {
            $type = $request->input("type");
            $price = $request->input("price");
            // TODO: validating
    
            $setting_table = SettingTable::where('id', $id)->first();
            if(!$setting_table) {
                return response()->json(
                    [
                        'error_code' =>  SettingTableErrorCode::SETTING_TABLE_NOT_FOUND, 
                        'message' => 'Setting table not found'
                    ], 400); 
            }
            
            if($type) $setting_table->type = $type;
            if($price) $setting_table->price = $price;
            $updated_setting_table = $setting_table->save();
    
            return response()->json([
                'message' => 'Successfully',
                'data' => $updated_setting_table
            ]);
        } catch(UniqueConstraintViolationException $e) {
            return response()->json(
                [
                    'error_code' =>  SettingTableErrorCode::SETTING_TABLE_ALREADY_EXIST, 
                    'message' => 'Setting table type already exist'
                ], 400); 
        }
    }
}

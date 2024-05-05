<?php

namespace App\Http\Controllers;

use App\Enums\Error\OrderErrorCode;
use App\Enums\Error\TableErrorCode;
use App\Enums\Error\UserErrorCode;
use App\Models\Table;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function check_in(Request $request) {
        $table_id = $request->input('table_id');
        $user_id = $request->input('user_id');
        // TODO: validation

        $table = Table::where('tables.id', $table_id)->join('setting_table', 'tables.setting_table_id', '=', 'setting_table.id')->first();
        if(!$table) {
            return response()->json(
                [
                    'error_code' =>  TableErrorCode::TABLE_NOT_FOUND, 
                    'message' => 'Table not found'
                ], 400); 
        }

        if(!$table->is_available) {
            return response()->json(
                [
                    'error_code' =>  TableErrorCode::TABLE_NOT_AVAILABLE, 
                    'message' => 'Table not available'
                ], 400); 
        }

        $user = User::where('id', $user_id)->first();
        if(!$user) {
            return response()->json(
                [
                    'error_code' =>  UserErrorCode::USER_NOT_FOUND, 
                    'message' => 'User not found'
                ], 400);
        }

        $insertOrder = [
            "start_time" => Carbon::now(),
            "end_time" => null,
            "current_price" => $table->price,
            "total_price" => null,
            "table_id" => $table_id,
            "user_id" => $user_id
        ];
        $new_order = Order::create($insertOrder);

        $updateTable = [
            "is_available" => false
        ];
        DB::table('tables')->where('id', $table_id)->update($updateTable);

        return response()->json([
            'message' => 'Successfully',
            'data' => $new_order
        ]);
    }

    public function check_out(Request $request) {
        // $order_id = $request->input('order_id');
        // // TODO: validation

        // $order = Order::where('id', $order_id)->first();
        // if(!$order) {
        //     return response()->json(
        //         [
        //             'error_code' =>  OrderErrorCode::ORDER_NOT_FOUND, 
        //             'message' => 'Order not found'
        //         ], 400); 
        // }

        // if($order->end_time) {
        //     return response()->json(
        //         [
        //             'error_code' =>  OrderErrorCode::ORDER_ALREADY_CHECK_OUT, 
        //             'message' => 'Order already checkout'
        //         ], 400); 
        // }
       

        // $insertOrder = [
        //     "start_time" => Carbon::now(),
        //     "end_time" => null,
        //     "current_price" => $table->price,
        //     "total_price" => null,
        //     "table_id" => $table_id,
        //     "user_id" => $user_id
        // ];
        // $new_order = Order::create($insertOrder);

        // $updateTable = [
        //     "is_available" => false
        // ];
        // DB::table('tables')->where('id', $$order->table_id)->update($updateTable);

        // return response()->json([
        //     'message' => 'Successfully',
        //     'data' => $new_order
        // ]);
    }

    public function find_many(Request $request) {
        $pageIndex = $request->input('pageIndex', 1); 
        $pageSize = $request->input('pageSize', 10);  

        $order_list = DB::table('orders')
            ->join('tables', 'orders.table_id', '=', 'tables.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'tables.name as tableName', 'users.name as employeeName')
            ->paginate($pageSize, ['*'], 'page', $pageIndex);

        return response()->json(
            [
                'message' => 'Successfully',
                'data' => $order_list
            ]);
    }
}

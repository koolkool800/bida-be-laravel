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
use Illuminate\Database\QueryException;
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
        $order_id = $request->input('order_id');
        // TODO: validation

        $order = Order::where('id', $order_id)->first();
        if(!$order) {
            return response()->json(
                [
                    'error_code' =>  OrderErrorCode::ORDER_NOT_FOUND, 
                    'message' => 'Order not found'
                ], 400); 
        }

        if($order->end_time) {
            return response()->json(
                [
                    'error_code' =>  OrderErrorCode::ORDER_ALREADY_CHECK_OUT, 
                    'message' => 'Order already checkout'
                ], 400); 
        }
       
        $time_diff = Carbon::now()->diff($order->start_time);
        $total_hours = $time_diff->days * 24 + $time_diff->h + $time_diff->i / 60 + $time_diff->s / 3600;
        $total_price = $total_hours * $order->current_price;
       
        $updateOrder = [
            "end_time" => Carbon::now(),
            "total_price" => intval(round($total_price)),
        ];
        DB::table('orders')->where('id', $order_id)->update($updateOrder);

        $updateTable = [
            "is_available" => true
        ];
        DB::table('tables')->where('id', $order->table_id)->update($updateTable);

        return response()->json([
            'message' => 'Successfully',
            'data' => 1
        ]);
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

    public function add_product_into_order(Request $request, $order_id) {
        try {
            $products = $request->input('products', null);
            
            if(!$products) {
                return response()->json([
                    'message' => 'Vui lòng thêm sản phẩm',
                    'data' => null
                ]);  
            }

            $order = Order::where('id', $order_id)->first();
            if(!$order) {
                return response()->json(
                    [
                        'error_code' =>  OrderErrorCode::ORDER_NOT_FOUND, 
                        'message' => 'Đơn hàng này không tìm thấy'
                    ], 400); 
            }
            if($order->end_time) {
                return response()->json(
                    [
                        'error_code' =>  OrderErrorCode::ORDER_ALREADY_CHECK_OUT, 
                        'message' => 'Đơn hàng này đã thanh toán'
                    ], 400); 
            }

            $insert_order_detail = [];

            foreach($products as $product) {
                array_push($insert_order_detail, [
                    "so_luong_san_pham" => $product['quantity'],
                    "gia_san_pham" => $product['price'],
                    "don_hang_id" => $order_id,
                    "san_pham_id" => $product['product_id']
                ]); 
            }
            DB::table('don_hang_chi_tiet')->insert($insert_order_detail);

            return response()->json([
                'message' => 'Thêm thành công',
                'data' => 1
            ]);
        } catch(QueryException $e) {
            print_r($e);
            return response()->json(
                [ 
                    'message' => 'Vui lòng kiểm tra lại sản phẩm'
                ], 
                400); 
        }
    }
}

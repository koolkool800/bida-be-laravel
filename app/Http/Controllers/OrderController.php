<?php

namespace App\Http\Controllers;

use App\Enums\Error\OrderErrorCode;
use App\Enums\Error\TableErrorCode;
use App\Enums\Error\UserErrorCode;
use App\Enums\InventoryType;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
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
        $customer_name = $request->input('customer_name', null);
        $customer_phone = $request->input('customer_phone', null);
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

        if(!$customer_name) {
            $customer_name = null;
        }

        $total_price_by_start_time_and_end_time = $this->calc_total_price_by_start_time_and_end_time($order->current_price, $order->start_time, Carbon::now());
        $total_price = $total_price_by_start_time_and_end_time + $order->tong_gia_san_pham;

        $updateOrder = [
            "end_time" => Carbon::now(),
            "total_price" => $total_price,
            "ten_khach_hang" => $customer_name,
            "customer_phone" => $customer_phone,
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
        $q = $request->input('q', null);
        $has_checkout = $request->input('has_checkout', null); 

        $query = DB::table('orders');

        if($has_checkout) {
            $has_checkout = ($has_checkout === 'false') ? false : true;
            if($has_checkout) {
                $query->whereNotNull('orders.end_time');
            } else {
                $query->whereNull('orders.end_time');
            }
        }
        if($q) {
            $query->where('tables.name', 'LIKE', '%' . $q . '%');
        }

        $order_list = $query
            ->join('tables', 'orders.table_id', '=', 'tables.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id as id',
                'orders.start_time as start_time',
                'orders.end_time as end_time',
                'orders.current_price as current_price',
                'orders.total_price as total_price',
                'orders.created_at as created_at',
                'orders.updated_at as updated_at',
                "orders.table_id as table_id",
                "orders.user_id as user_id",
                'orders.tong_gia_san_pham as total_product_price',
                'orders.ten_khach_hang as customer_name',
                'orders.customer_phone as customer_phone',
                'tables.name as tableName', 
                'users.name as employeeName'
                )
            ->orderBy('orders.created_at', 'DESC')
            ->paginate($pageSize, ['*'], 'page', $pageIndex);

        return response()->json(
            [
                'message' => 'Successfully',
                'data' => $order_list
            ]);
    }

    public function add_product_into_order(Request $request, $order_id) {
        DB::beginTransaction();
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
            $insert_data = [];
            foreach($products as $product) {
                $order->tong_gia_san_pham += $product['quantity'] * $product['price'];

                array_push($insert_order_detail, [
                    "so_luong_san_pham" => $product['quantity'],
                    "gia_san_pham" => $product['price'],
                    "don_hang_id" => $order_id,
                    "san_pham_id" => $product['product_id']
                ]); 

                array_push($insert_data, [
                    "quantity" => $product['quantity'],
                    "product_id" => $product['product_id'],
                    "date" => Carbon::now()->toDateString(),
                    "type" => InventoryType::EXPORT
                ]);  
                DB::table('san_pham')
                ->where('id', $product['product_id'])
                ->update(['quantity' => DB::raw('quantity - ' . $product['quantity'])]);
                
            }
 
            DB::table('inventory')->insert($insert_data);

            DB::table('orders')->where('id', $order_id)->update(["tong_gia_san_pham" => $order->tong_gia_san_pham]);
            DB::table('don_hang_chi_tiet')->insert($insert_order_detail);

            DB::commit();
            return response()->json([
                'message' => 'Thêm thành công',
                'data' => 1
            ]);

        } catch(QueryException $e) {
            DB::rollback();
            if ($e->getCode() === '22003') {
                return response()->json(
                    [ 
                        'message' => 'Vui lòng kiểm tra số lượng sản phẩm'
                    ], 
                    400); 
            }
            else return response()->json(
                [ 
                    'message' => 'Vui lòng kiểm tra lại sản phẩm'
                ], 
                400); 
        }
    }

    public function calc_total_price_by_order_id(Request $request, $order_id) {
        $order = Order::where('id', $order_id)->first();
        if(!$order) {
            return response()->json(
                [
                    'error_code' =>  OrderErrorCode::ORDER_NOT_FOUND, 
                    'message' => 'Đơn hàng này không tìm thấy'
                ], 400); 
        }

        $total_price;
        if($order->end_time) {
            $total_price = $order->total_price;
        } else {
            $total_price_by_start_time_and_end_time = $this->calc_total_price_by_start_time_and_end_time($order->current_price, $order->start_time, Carbon::now());
            $total_price = $total_price_by_start_time_and_end_time + $order->tong_gia_san_pham;
        }

        return response()->json([
            'message' => 'Successfully',
            'data' => [
                "total_price" => $total_price
            ]
        ]);

    }

    private function calc_total_price_by_start_time_and_end_time($price, $start_time, $end_time) {
        $time_diff = $end_time->diff($start_time);
        $total_hours = $time_diff->days * 24 + $time_diff->h + $time_diff->i / 60 + $time_diff->s / 3600;
        $total_price = $total_hours * $price;

        return intval(round($total_price));
    }

    public function find_one(Request $request, $order_id) {
        $order = Order::where('orders.id', $order_id)
        ->join('tables', 'orders.table_id', '=', 'tables.id')
        ->join('setting_table', 'tables.setting_table_id', '=', 'setting_table.id')
        ->select(
            'orders.id as id',
            'orders.start_time as start_time',
            'orders.end_time as end_time',
            'orders.current_price as current_price',
            'orders.total_price as total_price',
            'orders.created_at as created_at',
            'orders.updated_at as updated_at',
            "orders.table_id as table_id",
            "orders.user_id as user_id",
            'orders.tong_gia_san_pham as total_product_price',
            'orders.ten_khach_hang as customer_name',
            'orders.customer_phone as customer_phone',

            'tables.name as table_name',
            'setting_table.type as setting_table_type',
        )
        ->first();
        
        if(!$order) {
            return response()->json(
                [
                    'error_code' =>  OrderErrorCode::ORDER_NOT_FOUND, 
                    'message' => 'Đơn hàng này không tìm thấy'
                ], 400); 
        }
        $order_detail = OrderDetail::where('don_hang_chi_tiet.don_hang_id', $order_id)
        ->join('san_pham', 'san_pham.id', '=', 'don_hang_chi_tiet.san_pham_id')
        ->select(
            'san_pham.hinh_anh_url as image_url',
            'san_pham.ten_san_pham as product_name',
            'san_pham.loai_san_pham as product_type',
            'san_pham.id as product_id',

            'don_hang_chi_tiet.gia_san_pham as product_price',
            'don_hang_chi_tiet.thoi_gian_tao as created_at',
            'don_hang_chi_tiet.so_luong_san_pham as quantity'
        )
        ->orderBy('don_hang_chi_tiet.thoi_gian_tao', 'ASC')
        ->get();

        $order->order_detail = $order_detail;

        return response()->json(
            [
                'message' => 'Successfully',
                'data' => $order
            ]
        );
    }

    public function download_invoice(Request $request, $order_id) {
        $order = Order::where('orders.id', $order_id)
        ->join('tables', 'orders.table_id', '=', 'tables.id')
        ->join('setting_table', 'tables.setting_table_id', '=', 'setting_table.id')
        ->select(
            'orders.id as id',
            'orders.start_time as start_time',
            'orders.end_time as end_time',
            'orders.current_price as current_price',
            'orders.total_price as total_price',
            'orders.created_at as created_at',
            'orders.updated_at as updated_at',
            "orders.table_id as table_id",
            "orders.user_id as user_id",
            'orders.tong_gia_san_pham as total_product_price',
            'orders.ten_khach_hang as customer_name',
            'orders.customer_phone as customer_phone',

            'tables.name as table_name',
            'setting_table.type as setting_table_type',
        )
        ->first();
        
        if(!$order) {
            return response()->json(
                [
                    'error_code' =>  OrderErrorCode::ORDER_NOT_FOUND, 
                    'message' => 'Đơn hàng này không tìm thấy'
                ], 400); 
        }
        $order_detail = OrderDetail::where('don_hang_chi_tiet.don_hang_id', $order_id)
        ->join('san_pham', 'san_pham.id', '=', 'don_hang_chi_tiet.san_pham_id')
        ->select(
            'san_pham.hinh_anh_url as image_url',
            'san_pham.ten_san_pham as product_name',
            'san_pham.loai_san_pham as product_type',
            'san_pham.id as product_id',

            'don_hang_chi_tiet.gia_san_pham as product_price',
            'don_hang_chi_tiet.thoi_gian_tao as created_at',
            'don_hang_chi_tiet.so_luong_san_pham as quantity'
        )
        ->orderBy('don_hang_chi_tiet.thoi_gian_tao', 'ASC')
        ->get();

        $order->order_detail = $order_detail;
        // print_r($order);
        $order->price_table = isset($order->total_price) ? ($order->total_price - $order->total_product_price) : 0;

        $order->start_time = Carbon::parse($order->start_time, 'UTC')->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
        if($order->end_time) {
            $order->end_time = Carbon::parse($order->end_time, 'UTC')->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
        }

        $pdf = PDF::loadView('invoice', compact('order'));
        
        return $pdf->download('invoice.pdf');
    }
}

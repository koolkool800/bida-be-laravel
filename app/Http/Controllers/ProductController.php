<?php

namespace App\Http\Controllers;

use App\Enums\Error\ProductErrorCode;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private $product_image_default = 'https://theme.hstatic.net/1000300454/1000391697/14/blog_no_image.jpg';

    public function create(Request $request) {
        $name = $request->input('name');
        $price = $request->input('price');
        $type = $request->input('type');
        $image_url = $request->input('image_url', $this->product_image_default);
        // TODO: validating

        $product = Product::where('ten_san_pham', $name)->where('loai_san_pham', $type)->first();
        if($product) {
            return response()->json(
                [
                    'error_code' =>  ProductErrorCode::PRODUCT_ALREADY_EXIST, 
                    'message' => 'Sản phẩm này đã tồn tại'
                ], 400); 
        }

        $insertProduct = [
            "ten_san_pham" => $name,
            "gia_san_pham" => $price,
            "loai_san_pham" => $type,
            "hinh_anh_url" => $image_url
        ];
        $newProduct = Product::create($insertProduct);
        
        return response()->json([
            'message' => 'Successfully',
            'data' => 1
        ]);
    }

    public function update(Request $request) {

    }

    public function find_many(Request $request) {
        $pageIndex = $request->input('pageIndex', 1); 
        $pageSize = $request->input('pageSize', 10);
        $q = $request->input('type', null);  
        $type = $request->input('type', null);  

        if(!$pageIndex) $pageIndex = 1;
        if(!$pageSize) $pageSize = 10;
        

        $query = DB::table('san_pham');
        if($q) {
            $query->where('san_pham.ten_san_pham', 'LIKE', '%' . $q . '%');
        }
        if($type) {
            $query->where('san_pham.loai_san_pham', $type);
        }

        $product_list = $query->select(
            'san_pham.id as id', 
            'san_pham.ten_san_pham as name', 
            'san_pham.hinh_anh_url as image_url', 
            'san_pham.loai_san_pham as type', 
            'san_pham.gia_san_pham as price', 
            'san_pham.thoi_gian_tao as created_at'
            )
            ->orderBy('san_pham.thoi_gian_tao', 'DESC') 
            ->paginate($pageSize, ['*'], 'page', $pageIndex);
     
        return response()->json([
            'message' => 'Successfully',
            'data' => $product_list
        ]);
    }

    public function delete(Request $request, $id) {
        $product = Product::where('id', $id)->first();
        if(!$product) {
            return response()->json(
                [
                    'error_code' =>  ProductErrorCode::PRODUCT_NOT_FOUND, 
                    'message' => 'Sản phẩm này không tìm thấy'
                ], 400); 
        }

        $product->delete();
        
        return response()->json([
            'message' => 'Successfully',
            'data' => 1
        ]);
    }
}

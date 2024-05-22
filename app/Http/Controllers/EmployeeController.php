<?php

namespace App\Http\Controllers;

use App\Enums\Error\UserErrorCode;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function create(Request $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');
        $user_name = $request->input('user_name');
        $phone = $request->input('phone');
        $address = $request->input('address');
        // TODO: validating

        $is_exist_user_name = User::where('user_name', $user_name)->first();
        if($is_exist_user_name) {
            return response()->json(
                [
                    'error_code' =>  UserErrorCode::USER_ALREADY_EXIST, 
                    'message' => 'User name already exist'
                ], 400); 
        }

        $insertUser = [
            "name" => $name,
            "password" => Hash::make($password),
            "user_name" => $user_name,
            "role" => UserRole::STAFF,
            "phone" => $phone,
            "address" => $address
        ];

        $new_user = User::create($insertUser);
        unset($new_user['password']);

        return response()->json([
            'message' => 'Successfully',
            'data' => $new_user
        ]);
    }

    public function find_many(Request $request) {
        $pageIndex = $request->input('pageIndex', 1); 
        $pageSize = $request->input('pageSize', 10);  

        $user_list = User::where('role', UserRole::STAFF)
            ->select('id', 'name', 'created_at', 'updated_at', 'user_name', 'phone', 'address')
            ->paginate($pageSize, ['*'], 'page', $pageIndex);
 
        return response()->json([
            'message' => 'Successfully',
            'data' => $user_list
        ]);
    }
}

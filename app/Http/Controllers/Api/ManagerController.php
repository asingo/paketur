<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ManagerController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name',
            'email' => 'required|unique:users,email|email:rfc,dns',
            'password' => 'required|min:8|confirmed',
            'company' => 'required|numeric|exists:companies,id',
            'phone_number' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $users = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'manager',
                'password' => Hash::make($request->password)
            ]);
            $manager = Manager::create([
                'user_id' => $users->id,
                'company_id' => $request->company,
                'phone_number' => $request->phone_number
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'manager' => $manager
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 409);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name',
            'email' => 'required|unique:users,email|email:rfc,dns',
            'password' => 'required|min:8|confirmed',
            'company' => 'required|numeric|exists:companies,id',
            'phone_number' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $getManager = Manager::whereId($id)->first();
            $manager = Manager::whereId($id)->update([
                'user_id' => $getManager->user_id,
                'company_id' => $request->company
            ]);
            $users = User::whereId($getManager->user_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);


            DB::commit();
            return response()->json([
                'success' => true,
                'manager' => $manager
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 409);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Company::destroy($id);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Manager has been deleted'
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function get($id)
    {
        $manager = Manager::whereId($id)->first();
        return response()->json([
            'success' => true,
            'manager' => $manager
        ]);
    }

    public function show()
    {
        $manager = Manager::paginate(5);
        return response()->json([
            'success' => true,
            'manager' => $manager
        ]);
    }
}

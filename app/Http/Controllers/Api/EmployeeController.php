<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
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
                'role' => 3,
                'password' => Hash::make($request->password)
            ]);
            $employee = Employee::create([
                'user_id' => $users->id,
                'company_id' => $request->company,
                'phone_number' => $request->phone_number
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'employee' => $employee
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
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
            $getEmployee = Employee::whereId($id)->first();
            $users = User::whereId($getEmployee->user_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 3,
                'password' => Hash::make($request->password)
            ]);
            $employee = Employee::whereId($id)->update([
                'user_id' => $getEmployee->user_id,
                'company_id' => $request->company,
                'phone_number' => $request->phone_number
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'manager' => $employee
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Employee::destroy($id);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
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
        $employee = Employee::whereId($id)->first();
        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }

    public function show()
    {
        $employee = Employee::paginate(5);
        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }
}

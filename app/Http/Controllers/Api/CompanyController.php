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


class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:companies,name',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $company = Company::create([
                'name' => $request->name,
            ]);
            $user = User::create([
                'name' => 'Admin Manager' . $request->name,
                'email' => 'email@perusahaan.com',
                'role' => 'manager',
                'password' => Hash::make('password'),
            ]);
            $manager = Manager::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'phone_number' => 000
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'company' => $company,
                'message' => 'Company created successfully.'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:companies',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $company = Company::whereId($id)->update([
                'name' => $request->name
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'company' => $company,
                'message' => 'Company updated successfully.'
            ], 200);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }

    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            Company::destroy($id);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully.'
            ], 200);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ],409);
        }
    }

    public function show()
    {
        $company = Company::paginate(5);
        return response()->json([
            'success' => true,
            'company' => $company,
        ]);
    }
}

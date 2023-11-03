<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::with('company')->get();
        return response(['status_code' => 200, 'employees' => EmployeeResource::collection($employees)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = 200;

        try {
            $data = $request->all();

            $validator = Validator::make($data, [
                'first_name' => 'required|max:20',
                'last_name' => 'required|max:20',
                'company_id' => 'max:255',
                'email' => 'max:255',
                'phone' => 'max:255',
            ]);

            if ($validator->fails()) {
                return response(['status_code' => 422,'error' => $validator->errors(), 'Validation Error']);
            }

            Employee::create($data);

            $message = 'Employee stored successfully';
        } catch (\Illuminate\Database\QueryException $qe) {
            $status = 500;
            $message = $qe->getMessage();
        } catch (\Exception $ex) {
            $status = 500;
            $message = $ex->getMessage();
        } catch (\Throwable $t) {
            $status = 500;
            $message = $t->getMessage();
        }

        return response(['status_code' => $status, 'message' => $message]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::with('company')->find($id);

        return response(['status_code' => 200, 'employee' => new EmployeeResource($employee)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $status = 200;
        try {
            $data = $request->all();

            $validator = Validator::make($data, [
                'first_name' => 'required|max:20',
                'last_name' => 'required|max:20',
                'company_id' => 'max:255',
                'email' => 'max:255',
                'phone' => 'max:255',
            ]);

            if ($validator->fails()) {
                return response(['status_code' => 422,'error' => $validator->errors(), 'Validation Error']);
            }

            $employee->update($data);

            return response([
                'status_code' => $status,
                'employee' => new EmployeeResource($employee),
                'message' => 'Employee updated successfully'
            ]);
        } catch (\Illuminate\Database\QueryException $qe) {
            $status = 500;
            $message = $qe->getMessage();
        } catch (\Exception $ex) {
            $status = 500;
            $message = $ex->getMessage();
        } catch (\Throwable $t) {
            $status = 500;
            $message = $t->getMessage();
        }

        return response(['status_code' => $status, 'message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response(['status_code' => 200, 'message' => 'Employee deleted successfully']);
    }
}

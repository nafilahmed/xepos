<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyResource;
use Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companys = Company::all();
        return response(['status_code' => 200, 'companys' => CompanyResource::collection($companys)]);
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
                'name' => 'required|max:10',
                'email' => 'max:255',
                'website' => 'max:255',
            ]);

            if ($validator->fails()) {
                return response(['status_code' => 422,'error' => $validator->errors(), 'Validation Error']);
            }

            if(!empty($data['logo'])){
                $file = $request->file('logo');
                $name = '/logos/' . uniqid() . '.' . $file->extension();
                $file->storePubliclyAs('/public', $name);
                $data['logo'] = $name;
            }

            if(!empty($data['id'])){
                Company::where('id',$data['id'])->update($data);
            }else{
                Company::create($data);
            }


            $message = 'Company stored successfully';
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
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::find($id);

        return response(['status_code' => 200, 'company' => new CompanyResource($company)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response(['status_code' => 200, 'message' => 'Company deleted successfully']);
    }
}

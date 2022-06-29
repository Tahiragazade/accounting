<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Roles;

class CompanyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function all(Request $request): JsonResponse
    {
        paginate($request, $limit, $offset);
        $companyQuery = Company::query();

        if($request->has('name')) {
            $companyQuery->where('name', 'like', filter($request->get('name')));
        }
        if($request->has('voen')) {
            $companyQuery->where('voen', 'like', filter($request->get('voen')));
        }

        $count = $companyQuery->count();
        $companies = $companyQuery->limit($request->get('limit'))->offset($request->get('offset'))->get();


        return response()->json(['data' => $companies, 'total' => $count]);
    }
    public function store(Request $request){
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $validator = Validator::make($request->all(), [
            'name'=>['required','string','unique:companies'],
            'voen'=>['required','integer','unique:companies'],

        ]);
        if ($validator->fails())
        {
            return validationError($validator->errors());
        }
        $model= new Company();
        $model->name=$request->name;
        $model->voen=$request->voen;
        $model->save();

        return createSuccess($model);
    }
    public function update(Request $request)
    {
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $validator = Validator::make($request->all(), [
            'name'=>['string',Rule::unique('companies')->ignore($request->id)],
            'voen'=>['integer',Rule::unique('companies')->ignore($request->id)],
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }
        $model=Company::find($request->id);
        $model->name=$request->name;
        $model->voen=$request->voen;

        $model->save();
        return updateSuccess($model);
    }
    public function tree(Request $request): JsonResponse
    {
        paginate($request, $limit, $offset);
        $companyQuery = Company::query();

        if($request->has('name')) {
            $companyQuery->where('name', 'like', filter($request->get('name')));
        }

        $count = $companyQuery->count();
        $companies = $companyQuery->limit($request->get('limit'))->offset($request->get('offset'))->get();

        $data = simpleTree($companies);
        return response()->json(['data' => $data, 'total' => $count]);
    }
    public function single($id){
        $model= Company::query()->find($id);

        if (!$model) {
            return notFoundError($id);
        }

        return response()->json($model);
    }
    public function delete($id){
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $model= Company::query()->find($id);

        if (!$model) {
            return notFoundError($id);
        }
//        $delete=checkIfExist('Project','client_id',$id);
//        if($delete==1){
//            return notDeleteError();
//        }
        Company::where('id', '=', $id)->delete();
        return deleted();

    }
    //
}

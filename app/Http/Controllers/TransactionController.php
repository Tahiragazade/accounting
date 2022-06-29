<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Roles;

class TransactionController extends Controller
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
        $transactionQuery = Transaction::query();

        if($request->has('tr_id')) {
            $transactionQuery->where('tr_id', '=', filter($request->get('tr_id')));
        }
        if($request->has('from_company')) {
            $transactionQuery->where('from_company', '=', filter($request->get('from_company')));
        }
        if($request->has('to_company')) {
            $transactionQuery->where('to_company', '=', filter($request->get('to_company')));
        }
        if($request->has('amount')) {
            $transactionQuery->where('amount', '=', filter($request->get('amount')));
        }
        if($request->has('adv_amount')) {
            $transactionQuery->where('adv_amount', '=', filter($request->get('adv_amount')));
        }
        if($request->has('note')) {
            $transactionQuery->where('note', 'like', filter($request->get('note')));
        }
        if($request->has('status')) {
            $transactionQuery->where('status', '=', filter($request->get('status')));
        }
        if($request->has('from_date')) {
            $transactionQuery->where('created_at', '>=', filter($request->get('from_date')));
        }
        if($request->has('to_date')) {
            $transactionQuery->where('created_at', '<=', filter($request->get('to_date')));
        }

        $count = $transactionQuery->count();
        $transactions = $transactionQuery->limit($request->get('limit'))->offset($request->get('offset'))->get();
        foreach ($transactions as $transaction){
            $transaction->total=$transaction->amount+$transaction->adv_amount;
        }

        return response()->json(['data' => $transactions, 'total' => $count]);
    }
    public function store(Request $request){
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $validator = Validator::make($request->all(), [
            'tr_id'=>['required','string','unique:transactions'],
            'from_company'=>['required','integer'],
            'to_company'=>['required','integer'],
            'amount'=>['required','regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'adv_amount'=>['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'note'=>['string'],

        ]);
        if ($validator->fails())
        {
            return validationError($validator->errors());
        }
        $model = new Transaction();
        $model->tr_id=$request->tr_id;
        $model->from_company=$request->from_company;
        $model->to_company=$request->to_company;
        $model->amount=$request->amount;
        $model->status=1;
        $model->adv_amount=$request->adv_amount;
        $model->note=$request->note;
        $model->save();

        return createSuccess($model);
    }
    public function update(Request $request)
    {
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $validator = Validator::make($request->all(), [
            'tr_id'=>['string',Rule::unique('transactions')->ignore($request->id)],
            'from_company'=>['required','integer'],
            'to_company'=>['required','integer'],
            'amount'=>['required','regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'adv_amount'=>['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'note'=>['string'],
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }
        $model=Transaction::find($request->id);
        if($model->status!=1){
            return permissionError();
        }
        $model->tr_id=$request->tr_id;
        $model->from_company=$request->from_company;
        $model->to_company=$request->to_company;
        $model->amount=$request->amount;
        $model->adv_amount=$request->adv_amount;
        $model->note=$request->note;

        $model->save();
        return updateSuccess($model);
    }
    public function tree(Request $request): JsonResponse
    {
        paginate($request, $limit, $offset);
        $transactionQuery = Transaction::query();

        if($request->has('tr_id')) {
            $transactionQuery->where('tr_id', 'like', filter($request->get('tr_id')));
        }

        $count = $transactionQuery->count();
        $transactions = $transactionQuery->limit($request->get('limit'))->offset($request->get('offset'))->get();

        $data = simpleTree($transactions);
        return response()->json(['data' => $data, 'total' => $count]);
    }
    public function single($id){
        $model= Transaction::query()->find($id);

        if (!$model) {
            return notFoundError($id);
        }

        return response()->json($model);
    }
    public function delete($id){
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $model= Transaction::query()->find($id);

        if (!$model) {
            return notFoundError($id);
        }
//        $delete=checkIfExist('Project','client_id',$id);
//        if($delete==1){
//            return notDeleteError();
//        }
        Transaction::where('id', '=', $id)->delete();
        return deleted();

    }
    public function payment(Request $request){
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $validator = Validator::make($request->all(), [
            'tr_id'=>['string','required']
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }
        $transaction=Transaction::where(['tr_id'=>$request->tr_id])->first();
        if($transaction==null){
            return notFoundError($request->tr_id);
        }
        $transaction->status=2;
        $transaction->save();
        return response()->json($transaction);
    }
    public function regret(Request $request){
        if(checkRole()!=Roles::SYS_OWNER &&checkRole()!=Roles::COMPANY_OWNER){
            return permissionError();
        }
        $validator = Validator::make($request->all(), [
            'tr_id'=>['string','required'],
            'note'=>['string','required'],
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }
        $transaction=Transaction::where(['tr_id'=>$request->tr_id])->first();
        if($transaction==null){
            return notFoundError($request->tr_id);
        }
        $transaction->status=3;
        $transaction->note=$request->note;
        $transaction->save();
        return response()->json($transaction);
    }
    //
}

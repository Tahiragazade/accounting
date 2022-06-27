<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Roles {
    const SYS_OWNER = 0;
    const COMPANY_OWNER = 1;
    const TEAM_LEADER = 2;
    const PROJECT_MANAGER = 3;
    const WORKER = 4;
}
class TaskStatus{
    const TASK_WAITING =0;
    const TASK_STARTED =1;
    const WAITING_FOR_TECHNICAL_TASK =2;
    const WAITING_FOR_RESOURCE =3;
    const TASK_TESTING =4;
    const TASK_HAND_OVER =5;
    const PROJECT_HAND_OVER =6;
    const TASK_CANCELLED =7;
}
function validationError($errors){

    return response()->json([
        'message' => 'Məlumatları doğru daxil edin',
        'code' => 400,
        'error' => $errors,
    ], 400);
}
function permissionError(){

    return response()->json([
        'message' => 'Sizin bu əməliyyatı icra etməyə icazəniz yoxdur',
        'code' => 401,
//        'error' => $errors,
    ], 401);
}
function notFoundError($id){
    return response()->json([
        'message' => 'Məlumat Tapılmadı',
        'code' => 404,
        'error' => $id .' uyğun nəticə tapılmadı',
    ], 404);
}
function createSuccess($data){
    return response()->json([
        'message' => 'Məlumat Uğurla bazaya yazıldı',
        'code' => 200,
        'data' => $data ,
    ], 200);
}
function updateSuccess($data){
    return response()->json([
        'message' => 'Məlumat Uğurla Dəyişdirildi',
        'code' => 200,
        'data' => $data ,
    ], 200);
}
function checkIfExist($table,$column,$data){
    $class = 'App\Models\\' . $table;

    $model= $class::query()
        ->select('*')
        ->where(''.$column.'', $data)
        ->first();

    return $model != null ? 1 : 0;
}
function notDeleteError(){
    return response()->json([
        'message' => 'Məlumat Silinə Bilməz',
        'code' => 403,
        'data' => 'Bu id - ə bağlı başqa məlumatlar var' ,
    ], 403);
}
function deleted(){
    return response()->json([
        'message' => 'Məlumat Silindi',
        'code' => 200,
        'data' => 'Məlumat Uğurla Silindi' ,
    ], 200);
}
function checkRole(){
    $user=User::find(Auth::id());
    return $user->role;
}
function paginate(\Illuminate\Http\Request $request, &$limit, &$offset) {
    $limit = $request->has('limit') ? intval($request->get('limit')) : 10;
    $page = $request->has('page') ? intval($request->get('page')) - 1 : 0;
    $offset = ($page) * $limit;
}
function filter($val) {
    return "%$val%";
}
function simpleTree($datas){
    $tree = [];
    foreach ($datas as $data) {
        $tree[] = array(
            'key' => $data->id,
            'value' => $data->id,
            'title' => $data->name,
        );
    }
    return $tree;
}
function taskTree($datas){
    $tree = [];
    foreach ($datas as $data) {
        $tree[] = array(
            'key' => $data->id,
            'value' => $data->id,
            'title' => $data->task,
        );
    }
    return $tree;
}
function statusTree(){
    $tree = [];

        $tree[] = array(
            'key' => 0,
            'value' => 0,
            'title' => 'Gözləmədə',
        );
        $tree[] = array(
            'key' => 1,
            'value' => 1,
            'title' => 'Başlayıb',
        );
        $tree[] = array(
            'key' => 2,
            'value' => 2,
            'title' => 'Texniki Tapşırıq Gözlənilir',
        );
        $tree[] = array(
            'key' => 3,
            'value' => 3,
            'title' => 'Resurs Gözlənilir',
        );
        $tree[] = array(
            'key' => 4,
            'value' => 4,
            'title' => 'Test Mərhələsindədir',
        );
        $tree[] = array(
            'key' => 5,
            'value' => 5,
            'title' => 'Tapşırıq Təhvil Verilib',
        );
        $tree[] = array(
            'key' => 6,
            'value' => 6,
            'title' => 'Proyekt Təhvil Verilib',
        );
        $tree[] = array(
            'key' => 7,
            'value' => 7,
            'title' => 'Tapşırıq Ləğv Edilib',
        );


    return $tree;
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registros;
use Exception;

class IndexController extends Controller
{
    public function index(){
        return view("index");
    }
    
    public function getAllRegistros(){
        $registros = Registros::orderBy('created_at')->get();
        return $registros->toArray();
    }
    
    public function count(Request $request){
        $registros = "";
        if ($request->input('status')) {
            $registros = Registros::where('status', $request->input('status'))->orderBy('created_at')->get();
        } else {
            $registros = Registros::where('status', 1)->orderBy('created_at')->get();
        }
        return count($registros->toArray());
    }

    public function checkAll(Request $request){
        try {
            if (Registros::whereNotNull('created_at')->update(["status" => $request->input('status')=='true' ? 1 : 2])){
                return 1;
            } else {
                return 0;
            }
        } catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function addRegistro(Request $request){
        try {
            $registro = new Registros;
            $registro->valor = $request->input('value');
            $registro->status = 1;
            if ($registro->save()) {
                return 1;
            } else {
                return 0;
            }
        } catch(Exception $e){
           print_r($e->getMessage());
        }
    }

    public function editRegistro(Request $request){
        try {
            $id = $request->input('id');
            $valor = $request->input('valor');
            $status = $request->input('status');
            $registro = Registros::find($id);
            $registro->valor = $valor;
            $registro->status = $status;
            if ($registro->save()) {
                return 1;
            } else {
                return 0;
            }
        } catch(Exception $e){
           print_r($e->getMessage());
        }
    }

    public function filterRegistro(Request $request) {
        try {
            $registros = null;
            if ($request->input('status_id') != null) {
                $id = $request->input('status_id');
                $registros = Registros::where('status', $id)->orderBy('created_at')->get();
            } else {
                $registros = Registros::orderBy('created_at')->get();
            }
            return $registros->toArray();
        } catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteRegistro(Request $request) {
        try {
            $id = $request->input('id');
            $registro = Registros::find($id);
            if ($registro->delete()) {
                return 1;
            }
        } catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteCompleted(Request $request) {
        try {
            if (Registros::where('status', 2)->delete()) {
                return 1;
            } else {
                return 0;
            }
        } catch(Exception $e){
            print_r($e->getMessage());
        }
    }
}

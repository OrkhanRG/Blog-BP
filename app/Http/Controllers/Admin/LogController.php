<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $serachText = $request->search_text;
        $userSerachText = $request->user_search_text;
        $model = $request->model;
        $action = $request->action;
        $logs = Log::with([
            'loggable',
            'user'
        ])
            ->where(function ($query) use ($serachText, $model, $action){
                if (!is_null($model))
                {
                    $query->where('loggable_type', $model);
                }
                if (!is_null($action))
                {
                    $query->where('loggable_type', $action);
                }
                if (!is_null($serachText))
                {
                    $query->where(function ($q) use ($serachText){
                        $q->orWhere('data', 'LIKE', "%" . $serachText . "%")
                            ->orWhere('created_at', 'LIKE', "%" . $serachText . "%");
                    });
                }
            })
            ->whereHas('loggable')
            ->whereHas('user', function ($query) use ($userSerachText){
                $query->where('name', 'LIKE', "%" . $userSerachText . "%")
                    ->orWhere('username', 'LIKE', "%" . $userSerachText . "%")
                    ->orWhere('email', 'LIKE', "%" . $userSerachText . "%");
            })
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $actions = Log::ACTIONS;
        $models = Log::MODELS;

        return view('admin.logs.list', [
                'list' => $logs,
                'models' => $models,
                'actions' => $actions
            ]);
    }

    public function getLog(Request $request)
    {
        $id = $request->id;
        $dataType = $request->data_type;

        $log = Log::query()->with('loggable')->findOrFail($id);

        $logtype = $log->loggable_type;

        $data = json_decode($log->data);
        if ($dataType == "data")
        {
            return response()->json()->setData($data)->setStatusCode(200);
        }

        $data = $log->loggable;
        return view('admin.logs.model-log-view', compact( 'data', 'logtype'));
    }
}

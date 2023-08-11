<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $list = User::query()
            ->withTrashed()
            ->isAdmin($request->is_admin)
            ->status($request->status)
            ->serachText($request->search_text)
            ->paginate(10);

        return view('admin.users.list', compact('list'));
    }

    public function create()
    {
        return view('admin.users.create-update');
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->except('_token');
        $data['password'] = bcrypt($data['password']);
        $data['status'] = isset($data['status']) ? 1 : 0;
        User::create($data);

        alert()->success("Uğurlu", "İstifadəçi əlavə edildi")->showConfirmButton('yaxşı', '#3085d6')->autoClose(5000);
        return redirect()->route('users.index');
    }

    public function edit(Request $request, User $user)
    {
//        $user = User::findOrFail($request->id);
        return view('admin.users.create-update', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->except('_token');
        if (!is_null($data['password']))
        {
            $data['password'] = bcrypt($data['password']);
        }
        else
        {
            unset($data['password']);
        }

        $data['status'] = isset($data['status']) ? 1 : 0;

        $user->update($data);

        alert()->success("Uğurlu", "İstifadəçi məlumatları dəyişdirildi")->showConfirmButton('yaxşı', '#3085d6')->autoClose(5000);
        return redirect()->route('users.index');
    }

    public function delete(Request $request): JsonResponse
    {
       $user = User::query()->findOrFail($request->id);

//       $user->article()->delete();
//       $user->category()->delete();

       $user->delete();

        return response()
            ->json(['status' => 'success', 'message' => 'İstifadəçi silindi', 'data' => ''])
            ->setStatusCode(200);
    }

    public function restore(Request $request)
    {
        $user = User::withTrashed()->findOrFail($request->id);
        $user->restore();

        return response()
            ->json(['status' => 'success', 'message' => 'İstifadəçi geri gətirildi', 'data' => ''])
            ->setStatusCode(200);
    }

    public function changeStatus(Request $request)
    {
        $user = User::query()
            ->where('id', $request->id)
            ->first();

        if ($user)
        {
            $user->status = $user->status ? 0 : 1;
            $user->save();

            return response()
                ->json(['status' => 'success', 'message' => 'Status dəyişdirildi', 'user_status' => $user->status, 'data' => ''])
                ->setStatusCode(200);
        }

        return response()
            ->json(['status' => 'error', 'message' => 'Status dəyişdirilməsi ləvğ edildi'])
            ->setStatusCode(404);
    }

    public function changeIsAdmin(Request $request)
    {
        $user = User::query()
            ->where('id', $request->id)
            ->first();

        if ($user)
        {
            $user->is_admin = $user->is_admin ? 0 : 1;
            $user->save();

            return response()
                ->json(['status' => 'success', 'message' => 'Status dəyişdirildi', 'user_is_admin' => $user->is_admin, 'data' => $user])
                ->setStatusCode(200);
        }

        return response()
            ->json(['status' => 'error', 'message' => 'Status dəyişdirilməsi ləvğ edildi'])
            ->setStatusCode(404);
    }
}

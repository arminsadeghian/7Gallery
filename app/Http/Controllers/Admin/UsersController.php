<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\StoreRequest;
use App\Http\Requests\Admin\Users\UpdateRequest;
use App\Models\User;

class UsersController extends Controller
{
    public function all()
    {
        $users = User::paginate(10);
        return view('admin.users.all', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();
        $createdUser = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'role' => $validatedData['role'],
        ]);

        if (!$createdUser) {
            return back()->with('failed', 'کاربر ایجاد نشد!');
        }

        return back()->with('success', 'کاربر ایجاد شد');

    }

    public function delete(int $userId)
    {
        $user = User::findOrFail($userId);
        $deletedUser = $user->delete();

        if (!$deletedUser) {
            return back()->with('failed', 'کاربر حذف نشد!');
        }

        return back()->with('success', 'کاربر حذف شد');
    }

    public function edit(int $userId)
    {
        $user = User::findOrFail($userId);
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateRequest $request, int $userId)
    {
        $validatedData = $request->validated();
        $user = User::findOrFail($userId);
        $updatedUser = $user->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'role' => $validatedData['role'],
        ]);

        if (!$updatedUser) {
            return back()->with('failed', 'کاربر آپدیت نشد!');
        }

        return back()->with('success', 'کاربر آپدیت شد');

    }

}

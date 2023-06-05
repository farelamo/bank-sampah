<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function checkPassword($request)
    {
        $rules = [
            'password' => 'required|max:8',
        ];

        Validator::make($request->all(), $rules, $messages =
        [
            'password.required' => 'password must be filled',
            'password.max' => 'maximal password is 8 character',
        ])->validate();
    }

    public function checkUsername($request)
    {
        $rules = [
            'username' => 'required|max:10|unique:users,username',
        ];

        Validator::make($request->all(), $rules, $messages =
        [
            'username.required' => 'email must be filled',
            'username.max' => 'maximal username is 10 character',
            'username.unique' => 'username has already been taken',
        ])->validate();
    }

    public function index(Request $request)
    {
        if (!$request->role) {
            return $this->returnCondition(false, 400, 'role must be filled');
        }

        if (auth()->user()->role == 'admin') {
            if ($request->role != 'nasabah') {
                return $this->returnCondition(false, 400, 'invalid role access');
            }
        }

        if (!in_array($request->role, ['admin', 'nasabah'])) {
            return $this->returnCondition(false, 404, 'role doesnt exists');
        }

        $users = User::select('id', 'name', 'username', 'phone', 'address', 'role')
            ->where('role', $request->role)
            ->paginate(5);

        return new UserCollection($users);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
        }

        if (auth()->user()->role == 'admin') {
            if ($user->role != 'nasabah') {
                return $this->returnCondition(false, 422, 'invalid role access');
            }
        }

        return new BaseResource($user);
    }

    public function store(UserRequest $request)
    {
        $this->checkUsername($request);
        $this->checkPassword($request);

        try {

            $createdData = [
                'name' => $request->name,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'balance' => 0,
            ];

            if (auth()->user()->role == 'superadmin') {
                if (!$request->role) {
                    return $this->returnCondition(false, 400, 'role must be filled');
                }

                if (!in_array($request->role, ['admin', 'nasabah'])) {
                    return $this->returnCondition(false, 404, 'role doesnt exists');
                }

                $createdData['role'] = $request->role;
            }

            if (auth()->user()->role == 'admin') {
                $createdData['balance'] = 0;
                $createdData['role'] = 'nasabah';
            }

            User::create($createdData);

            return $this->returnCondition(true, 200, 'Successfully created data');
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function update($id, UserRequest $request)
    {
        try {

            $user = User::where('id', $id)->first();
            if (!$user) {
                return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
            }

            if (auth()->user()->role == 'admin') {
                if ($user->role != 'nasabah') {
                    return $this->returnCondition(false, 422, 'Invalid role access');
                }
            }

            $updateData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'balance' => $request->balance,
            ];

            if ($request->username) {
                $this->checkUsername($request);
                $updateData['username'] = $request->username;
            }

            if ($request->password) {
                $this->checkPassword($request);
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            return $this->returnCondition(true, 200, 'Successfully updated data');
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::where('id', $id)->first();
            if (!$user) {
                return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
            }

            if (auth()->user()->role == 'admin') {
                if ($user->role != 'nasabah') {
                    return $this->returnCondition(false, 422, 'invalid role access');
                }
            }

            $user->garbage_deposits()->delete();
            $user->withdraw()->delete();
            $user->delete();

            return $this->returnCondition(true, 200, 'Successfully deleted data');
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal server error');
        }
    }
}

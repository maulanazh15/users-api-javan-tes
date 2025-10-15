<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UsersResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(UserLoginRequest $request): UsersResource
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();


        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "email atau password salah"
                    ]
                ]
            ], 401));
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UsersResource($user))->additional(['meta' => [
            'token' => $token
        ]]);

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return UsersResource::collection($users)->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();
        if (User::where('email', $data['email'])->count() == 1) {
            throw new HttpResponseException(response([
                "errors" => [
                    "email" => [
                        "email sudah terdaftar"
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UsersResource($user))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return (new UsersResource($user))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->all();
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return (new UsersResource($user))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

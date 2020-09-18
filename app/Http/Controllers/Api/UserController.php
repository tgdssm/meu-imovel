<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $users;

    public function __construct(User $user)
    {
        $this->users = $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->users->get();
        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();

        if (!$request->filled('password')) {
            $message = new ApiMessages('Usuario precisa de uma senha.');
            return response()->json($message->getMessage());
        }

        Validator::make($data, [
            'phone' => ['required'],
            'mobile_phone' => ['required'],
        ])->validate();

        try {
            $data['passoword'] = Hash::make($data['password']);

            $user = $this->users->create($data);
            $user->userProfile()->create([
                'phone' => $data['phone'],
                'mobile_phone' => $data['mobile_phone']
            ]);

            return response()->json([
                'data' => [
                    'msg' => 'usuario criado com sucesso!'
                ]
            ]);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = $this->users->with('userProfile')->findOrFail($id);
            $user->userProfile->social_networks = unserialize($user->userProfile->social_networks);
            return $user;
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        Validator::make($data, [
            'profile.phone' => ['required'],
            'profile.mobile_phone' => ['required'],
        ])->validate();

        try {

            $profile = $data['profile'];
            $profile['social_networks'] = serialize($profile['social_networks']);

            $user = $this->users->findOrFail($id);
            $user->update($data);
            $user->userProfile()->update($profile);
            return response()->json([
                'data' => [
                    'message' => ' User profile updated successfully!'
                ]
            ]);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = $this->users->findOrFail($id);
            $user->delete();
            return response()->json([
                'data' => [
                    'msg' => 'usuario excluido com sucesso!'
                ]
            ]);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @subgroup Auth
 */
class AuthController extends Controller
{
    use ApiResponse;

    public function getUser($request)
    {
        return User::where('email', $request->email)->first() ?? null;
    }

    /**
     * Login
     *
     * @bodyParam email string required Email of the admin. Example: admin@admin.com
     * @bodyParam password string required Password. Example: admin@123
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->input(),
                [
                    'email' => 'required|email|exists:users,email',
                    'password' => 'required|min:1|max:16',
                ]
            );

            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }

            $user = User::where('email', $request->email)->first() ?? null;

            if (!$user) {
                return $this->error('User not found');
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user->token = $user->createToken(env('APP_NAME'))->plainTextToken;
                return $this->success('Logged in successfully', new UserResource($user));
            }

            return $this->error('Invalid credentials');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Logout
     *
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->success('Logged out successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Logout Of All Devices
     *
     */
    public function logoutAll(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->success('Logged out of all devices successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}

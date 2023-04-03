<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function __construct()
    {
        //
    }

    /**
     * Create/register user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation errors.', $validator->errors());
        }

        // save user's data
        DB::beginTransaction();

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ];

            $user = User::create($userData);
            $token = $user->createToken('API Token')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Register success.',
                'token' => $token
            ]);
            //
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Failed register user', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed register user.', [], 500);
        }
    }

    /**
     * Authenticate user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation errors.', $validator->errors());
        }

        $loginData = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // check credentials & create user's token
        if (auth()->attempt($loginData)) {
            $token = $request->user()->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Logged in.',
                'token' => $token
            ]);
        }

        return $this->sendError('Log in failed.');
    }

    /**
     * Log out user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke user's token
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse('User logged out.');
    }
}

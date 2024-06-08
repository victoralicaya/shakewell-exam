<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\MailService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $vocuherService;
    private $mailService;

    public function __construct(VoucherService $voucherService, MailService $mailService)
    {
        $this->vocuherService = $voucherService;
        $this->mailService = $mailService;
    }

    public function register(UserRegistrationRequest $request)
    {
        try {
            $user = $this->createUser($request->validated());

            $code = $this->vocuherService->generateVoucherCode();

            $this->vocuherService->storeVoucher($user, $code);

            $this->mailService->sendRegistrationMail($user, $code);

            return response()->json([
                'message' => 'User has been registered.',
                'data' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(UserLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Successfully Logged-In.',
                'token' => $token,
                'data' => new UserResource($user),
            ], 200);
        } else {
            return response()->json([
                'message' => 'Wrong email or password.'
            ], 401);
        }
    }

    public function createUser(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'first_name' => $data['first_name'],
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
        ]);
    }
}

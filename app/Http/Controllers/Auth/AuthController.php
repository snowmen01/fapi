<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Repositories\Interface\AuthRepositoryInterface;
use App\Repositories\Interface\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $authRepository;
    protected $userRepository;

    public function __construct(
        AuthRepositoryInterface $authRepository,
        UserRepositoryInterface $userRepository,
    ) {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);

        $this->authRepository = $authRepository;
        $this->userRepository = $userRepository;
    }

    public function loginUser($user, $refreshToken, $refreshTokenExpiry)
    {
        $user->refresh_token = $refreshToken;
        $user->refresh_token_expried = $refreshTokenExpiry;
        $user->access_token = JWTAuth::fromUser($user);
        $user->update();

        return $user->access_token;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->only('email', 'password');
        auth()->attempt($data);
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => __('common.auth.failed')
            ], 401);
        }

        if ($user->active != config('constant.active')) {
            return response()->json([
                'error' => __('common.auth.inactive')
            ], 403);
        }

        if ($user->deleted_at != null) {
            return response()->json([
                'error' => __('common.auth.failed')
            ], 403);
        }

        $refreshToken = Str::random(64);
        $refreshTokenExpiry = now()->addDays(7);

        $userWithTokens = $this->loginUser($user, $refreshToken, $refreshTokenExpiry);

        return response()->json([
            'user' => auth()->user(),
            'access_token' => $userWithTokens,
        ]);
    }

    public function refresh(Request $request)
    {
        $user = $this->userRepository->getUserbyRefreshToken($request->all());
        if (!$user) {
            return response()->json([
                'status' => 1,
                'message' =>  __('api.refresh_token.not_found'),
            ], 400);
        }
        JWTAuth::setToken($user->access_token)->invalidate();
        $token = JWTAuth::fromUser($user);
        $responseData = $this->userRepository->updateRefreshToken($user->id, $token);
        return response()->json([
            'status' => 200,
            'message' =>  'Đăng nhập thành công',
            'access_token' => $token,
            'refresh_token' => $responseData->refresh_token,
        ], 200);
        return response()->json([
            'status' => 1,
            'message' =>  __('api.refresh_token.not_found'),
        ], 401);
    }

    public function logout()
    {
        if (auth()->check()) {
            auth()->logout();
            return response()->json([
                'status'  => 'success',
                'message' => __('common.auth.logout_success')
            ], 200);
        }
    }
}
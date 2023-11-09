<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\Web\MailResetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\Interface\AuthRepositoryInterface;
use App\Repositories\Interface\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;

class AuthController extends Controller
{
    protected $authRepository;
    protected $userRepository;

    public function __construct(
        AuthRepositoryInterface $authRepository,
        UserRepositoryInterface $userRepository,
    ) {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh', 'getReset', 'postReset']]);

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

    public function getReset(Request $request)
    {
        $data = $request->all();

        if ($data['email']) {
            $user = User::where('email', $data['email'])->first();
        }

        if ($data['phone']) {
            $user = User::where('phone', $data['phone'])->first();
        }

        if ($user) {
            $otpCode = rand(100000, 999999);

            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'token' => $otpCode,
                    'created_at' => now(),
                ]
            );

            if ($passwordReset) {
                Mail::to($user)->queue(new MailResetPassword($user, $otpCode));
                return response()->json([
                    'message' => "Gửi email thành công, vui lòng check email để lấy mã đặt lại mật khẩu!"
                ]);
            }
        } else {
            return response()->json([
                'message' => "Không tìm thấy tài khoản này, vui lòng cung cấp email hoặc số điện thoại chính xác!"
            ], 422);
        }
    }

    public function postReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required', 'numeric'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
            'password_confirm' => ['required', 'same:password'],
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }      

        $data = $request->all();

        $passwordReset = PasswordReset::where('token', $data['otp'])->first();
        if (Carbon::parse($passwordReset->created_at)->addMinutes(1)->isPast()) {
            return response()->json([
                'message' => "OTP Đã hết hạn, vui lòng thử lại."
            ], 422);
        }

        $user = User::where('email', $passwordReset->email)->first();
        $user->update([
            'password' => $data['password'],
        ]);
        if ($user) {
            $passwordReset->delete();
            return response()->json([
                'message' => "Cập nhật mật khẩu thành công!"
            ], 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->only('email', 'password');
        auth()->attempt($data);
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => __('common.auth.failed')
            ]);
        }

        if ($user->active != config('constant.active')) {
            return response()->json([
                'message' => __('common.auth.inactive')
            ]);
        }

        if ($user->deleted_at != null) {
            return response()->json([
                'message' => __('common.auth.failed')
            ]);
        }

        $refreshToken = Str::random(64);
        $refreshTokenExpiry = now()->addDays(7);

        $userWithTokens = $this->loginUser($user, $refreshToken, $refreshTokenExpiry);

        return response()->json([
            'message' =>  'Đăng nhập thành công',
            'access_token' => $userWithTokens,
            'refresh_token' => $user->refresh_token,
            'user' => $user,
        ], 200);
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
            'message' =>  'Đăng nhập thành công',
            'access_token' => $token,
            'user' => $user,
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth; 
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpCodeMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'name' => 'required|string|max:255',  
            'email' => 'required|email|unique:users,email',  
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');
        if (!$userRoleId) {
            return response()->json(['message' => 'Role user tidak ditemukan'], 500);
        }
        try {
            $user = User::create([
                'name' => $request->name,  
                'email' => $request->email,  
                'password' => Hash::make($request->password),  
                'role_id' => $userRoleId
            ]);
            // Kirim email (contoh kirim kata selamat datang, dsb.)
            Mail::raw('Terima kasih sudah mendaftar. Silakan generate OTP untuk verifikasi email.', function($m) use($user){
                $m->to($user->email)->subject('Selamat Datang di ReviewFilm');
            });
            return response()->json([
                'message' => 'User berhasil di-register',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mendaftar user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateOtpCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User dengan email tsb. tidak ditemukan'], 404);
        }
        $otp = rand(100000, 999999);
        $validUntil = Carbon::now()->addMinutes(5);

        OtpCode::updateOrCreate(
            ['user_id' => $user->id],
            ['otp' => $otp, 'valid_until' => $validUntil]
        );
        Mail::to($user->email)->send(new SendOtpCodeMail($otp));
        return response()->json([
            'success' => true,
            'message' => 'OTP Code Berhasil di generate'
        ], 200);
    }

    public function verificationEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $otpRecord = OtpCode::where('otp', $request->otp)->first();
        if (!$otpRecord) {
            return response()->json([
                'response_code' => '01',
                'response_message' => 'OTP Code tidak ditemukan'
            ], 404);
        }
        if (Carbon::now()->gt($otpRecord->valid_until)) {
            return response()->json([
                'response_code' => '01',
                'response_message' => 'otp code sudah tidak berlaku, silahkan generate ulang'
            ], 400);
        }
        $user = User::find($otpRecord->user_id);
        $user->email_verified_at = Carbon::now();
        $user->save();
        $otpRecord->delete();
        return response()->json([
            'response_code' => '00',
            'response_message' => 'email sudah terverifikasi'
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'email' => 'required|email',  
            'password' => 'required|string|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $credentials = $request->only('email', 'password');  
            if (!$token = auth()->guard('api')->attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            $user = auth()->guard('api')->user();
            return response()->json([
                'message' => 'User berhasil login',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Logout Berhasil'], 200);
    }

    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }
            $user->load('role');
            return response()->json([
                'message' => 'Profile berhasil ditampilkan',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return response()->json([
            'message' => 'User berhasil diperbarui',
            'user' => $user
        ], 200);
    }
}

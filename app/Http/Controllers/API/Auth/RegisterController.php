<?php

namespace App\Http\Controllers\API\Auth;


use App\Helpers\Helper;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class RegisterController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $request->merge([
            'terms' => filter_var($request->terms, FILTER_VALIDATE_BOOLEAN),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'terms' => $request->terms,
                'role' => 'User',
            ]);

            // Send OTP
            $otp = $this->send_otp($user);

            if (!$otp) {
                throw new \Exception('Failed to send OTP.');
            }

            DB::commit();
            return $this->success('Registered successfully.', ['otp' => $otp->token,'email' => $user->email], 201);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function send_otp(User $user,$mailType = 'verify')
    {
        $otp  = (new Otp)->generate($user->email, 'numeric', 4, 60);
        $message = $mailType === 'verify' ? 'Verify Your Email Address' : 'Reset Your Password';
//        \Mail::to($user->email)->send(new \App\Mail\OTP($otp->token,$user,$message,$mailType));
        return $otp;
    }

    public function resend_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            if($user){
                $otp = $this->send_otp($user);
                return $this->success('OTP send successfully.',['otp' => $otp->token],201);
            }else{
                return $this->error('Email not found',404);
            }
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function verify_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string|digits:4',
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return $this->error('Email not found',404);
            }

            if($user->email_verified_at !== null){
                return $this->error('Email already verified',404);
            }

            $verify = (new Otp)->validate($request->email, $request->otp);
            if($verify->status){
                $user->email_verified_at = now();
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Email verified successfully',
                    'token_type' => 'Bearer',
                    'token' => $user->createToken('AuthToken')->plainTextToken,
                    'data' => $user
                ]);
            }else{
                return $this->error($verify->message,404);
            }
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function forgot_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        try {
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return $this->error('Email not found',404);
            }
            $otp = $this->send_otp($user,'forget');
            return $this->success('OTP send successfully.',['otp' => $otp->token],201);
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function forgot_verify_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string|digits:4',
        ]);

        $verify = (new Otp)->validate($request->email, $request->otp);
        if($verify->status){
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return Helper::jsonErrorResponse('Email not found',404);
            }
            $user->reset_code = \Str::random(40);
            $user->reset_code_expires_at = Carbon::now()->addDays(1);
            $user->save();
            return $this->success('OTP verified successfully',[
                'token' => $user->reset_code,
            ],201);
        }else{
            return $this->error($verify->message,404);
        }
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        try {
            $user = User::where('reset_code', $request->token)->first();

            if(!$user){
                return $this->error('Invalid Token',404);
            }

            if ($user->reset_code_expires_at < Carbon::now()) {
                return $this->error('Token expired', 404);
            }

            $user->password = Hash::make($request->password);
            $user->reset_code = null;
            $user->reset_code_expires_at = null;
            $user->save();

            return $this->ok('Password reset successfully');

        }catch (\Exception $exception){
            return $this->error($exception->getMessage(),404);
        }
    }
}

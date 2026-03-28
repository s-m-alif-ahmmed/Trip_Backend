<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserDataResource;
use App\Models\User;
use App\Models\UserFollower;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileUpdateController extends Controller
{
    use ApiResponse;

    public function changePassword(Request $request)
    {
        $validator = $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->error('Incorrect Current Password', 402);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            return $this->ok('Password changed successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function updateDetails(UpdateUserRequest $request)
    {
        $user = Auth::user();

        if ($request->hasFile('avatar')) {

            if ($user->avatar) {
                Helper::fileDelete($user->avatar);
            }

            $file = $request->file('avatar');

            $nameWithoutExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            // Remove spaces + special characters
            $cleanName = Str::slug($nameWithoutExt);
            $fileName = time() . '_' . $cleanName . '.' . $extension;
            $imagePath = Helper::fileUpload($file, 'avatar', $fileName);
            $user->avatar = $imagePath;
        }

        $update = $user->update([
            "number"           => $request->number ?? $user->number,
            "address"       => $request->address ?? $user->address,
            "email"      => $request->email ?? $user->email,
            "name"        => $request->name ?? $user->name,
        ]);

        if ($update) {
            $data = new UserDataResource($user->fresh());
            return $this->ok('Profile info updated successfully', $data, 200);
        } else {
            return $this->error('Profile info not found', 500);
        }
    }

    public function accountDelete(Request $request)
    {
        $user = Auth::user();

        $user->delete();

        return $this->ok('Account deleted successfully', [], 200);
    }

}

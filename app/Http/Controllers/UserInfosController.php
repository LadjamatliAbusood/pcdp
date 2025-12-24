<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\user_infos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserInfosController extends Controller
{
    public function saveUserInfo(Request $request)
    {
        $request->validate([
            'nickName' => 'nullable|string',
            'firstName' => 'required|string',
            'middleName' => 'nullable|string',
            'lastName' => 'required|string',
            'extName' => 'nullable|string',
            'contactNumber' => 'required|string',
           
        ]);

        error_log(message: "N I C E");

        // $user = auth()->user();
        $user = Auth::guard('api')->user(); // Ensure Passport Auth works

        if (!$user) {
            error_log(message: "No User");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Save user info
        user_infos::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nickname' => $request->nickName,
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'ext_name' => $request->extName,
                'contact_number' => $request->contactNumber,
                
            ]
        );

        // Mark user as completed
        
         $user->update(['setup_at' => now()]);

        return response()->json([
            'message' => 'Onboarding completed successfully',
            'setup_done' => true
        ]);
    }
}

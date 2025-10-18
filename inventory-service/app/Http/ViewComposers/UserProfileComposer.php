<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class UserProfileComposer
{
    public function compose(View $view)
    {
        // If user is logged in but profile picture is not in session, fetch it
        if (session('user_id') && !session('user_profile_picture')) {
            try {
                $response = Http::get('http://127.0.0.1:8000/api/users/' . session('user_id'));
                
                if ($response->successful()) {
                    $user = $response->json();
                    session([
                        'user_profile_picture' => $user['profile_picture_url'] ?? null,
                        'user_department' => $user['department'] ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                // Silent fail - use default avatar
            }
        }
    }
}


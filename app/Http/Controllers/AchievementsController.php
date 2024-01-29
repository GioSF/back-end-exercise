<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return response()->json(
            $user->getUserAccomplishmentsData()
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $lessons = Lesson::factory()
            ->count(100)
            ->create();
        $users = User::factory()
            ->count(20)
            ->create();
        $comment = Comment::factory()
            ->count(500)
            ->create();
        $counter = 0;

        $lessons = Lesson::all();

        $users = User::all();

        foreach ($users as $user)
        {
            foreach($lessons as $lesson)
            {
                $isWatched = false;
                /* Change the proportion on (un)watched seeded lessons by increasing the second argument */
                // $isWatched = (bool) rand(0,2);
                $lesson->users()->attach($user->id, ['watched' => $isWatched]);
            }
        }
    }
}


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
            ->count(200)
            ->create();
        $users = User::factory()
            ->count(20)
            ->create();
        $comment = Comment::factory()
            ->count(500)
            ->create();
        $counter = 0;

        $lessons = Lesson::all();
        $counter = 51;

        $users = User::all();

        foreach ($users as $user)
        {
            $lessons = Lesson::where('id', '<', $counter)->get();
            foreach($lessons as $lesson)
            {
                // $isWatched = true;
                $isWatched = (bool) rand(0,2);
                $user->lessons()->attach($lesson->id, ['watched' => $isWatched]);
            }
            $counter = $counter - 7;
        }
    }
}


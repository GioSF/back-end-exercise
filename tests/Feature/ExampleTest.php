<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class ExampleTest extends TestCase
{
    // use RefreshDatabase;
    /**
     * A basic test example.
     */

    public function test_lesson_achievements_response(): void
    {
        $user = User::find(1);
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200);
        $zeroLessonsStatus = [
            "next_available_achievements" => [
              0 => "First Lesson Watched",
              1 => "First Comment Written",
            ],
            "current_badge" => "Beginner",
            "next_badge" => "Intermediate",
            "remaining_to_unlock_next_badge" => 4
        ];
        $firstLessonStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
            ],
            "next_available_achievements" => [
              0 => "5 Lessons Watched",
              1 => "First Comment Written",
            ],
            "current_badge" => "Beginner",
            "next_badge" => "Intermediate",
            "remaining_to_unlock_next_badge" => 3
        ];
        $fiveLessonsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
            ],
            "next_available_achievements" => [
              0 => "10 Lessons Watched",
              1 => "First Comment Written",
            ],
            "current_badge" => "Beginner",
            "next_badge" => "Intermediate",
            "remaining_to_unlock_next_badge" => 2
        ];
        $tenLessonsstatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
            ],
            "next_available_achievements" => [
              0 => "25 Lessons Watched",
              1 => "First Comment Written",
            ],
            "current_badge" => "Beginner",
            "next_badge" => "Intermediate",
            "remaining_to_unlock_next_badge" => 1
        ];
        $twentyFiveLessonsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
            ],
            "next_available_achievements" => [
              0 => "50 Lessons Watched",
              1 => "First Comment Written",
            ],
            "current_badge" => "Intermediate",
            "next_badge" => "Advanced",
            "remaining_to_unlock_next_badge" => 4
        ];
        $fiftyLessonsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
                4 => "50 Lessons Watched",
            ],
            "next_available_achievements" => [
              0 => "First Comment Written",
            ],
            "current_badge" => "Intermediate",
            "next_badge" => "Advanced",
            "remaining_to_unlock_next_badge" => 3
        ];

        $response->assertExactJson($zeroLessonsStatus, $strict = false);
        $lessonsAchievementsList = Lesson::lessonsWatchedAchievementsMap();
        $lessons = Lesson::all();

        foreach($lessons as $lesson)
        {
            $lesson->users()->attach($user->id, ['watched' => true]);
            $lessonsWatchedCounter = $user->lessons()->watched()->count();

            foreach($lessonsAchievementsList as $amount => $label)
            {
                if (($lessonsWatchedCounter == $amount) && ($amount == 1))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($firstLessonStatus, $strict = false);
                }
                if (($lessonsWatchedCounter > 1) && ($lessonsWatchedCounter < 5) && ($amount == 1))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($firstLessonStatus, $strict = false);
                }
                if (($lessonsWatchedCounter == $amount) && ($amount == 5))
                {
                    // Tries to "watch" the same lesson various times - it must not trigger new achievements
                    $lesson->users()->updateExistingPivot($user->id, ['watched' => true]);
                    $lesson->users()->updateExistingPivot($user->id, ['watched' => true]);
                    $lesson->users()->updateExistingPivot($user->id, ['watched' => true]);
                    $lesson->users()->updateExistingPivot($user->id, ['watched' => true]);
                    $lesson->users()->updateExistingPivot($user->id, ['watched' => true]);
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($fiveLessonsStatus, $strict = false);
                }
                if (($lessonsWatchedCounter > 5) && ($lessonsWatchedCounter < 10) && ($amount == 5))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($fiveLessonsStatus, $strict = false);
                }
                if (($lessonsWatchedCounter == $amount) && ($amount == 10))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($tenLessonsstatus, $strict = false);
                }
                if (($lessonsWatchedCounter > 10) && ($lessonsWatchedCounter < 25) && ($amount == 10))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($tenLessonsstatus, $strict = false);
                }
                if (($lessonsWatchedCounter == $amount) && ($amount == 25))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($twentyFiveLessonsStatus, $strict = false);
                }
                if (($lessonsWatchedCounter > 25) && ($lessonsWatchedCounter < 50) && ($amount == 25))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($twentyFiveLessonsStatus, $strict = false);
                }
                if (($lessonsWatchedCounter == $amount) && ($amount == 50))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($fiftyLessonsStatus, $strict = false);
                }
                if (($lessonsWatchedCounter > 50) && ($amount == 50))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($fiftyLessonsStatus, $strict = false);
                }
            }
        }
    }

    public function test_comment_achievements_response(): void
    {
        $user = User::find(1);

        $zeroCommentsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
                4 => "50 Lessons Watched",
            ],
            "next_available_achievements" => [
              0 => "First Comment Written",
            ],
            "current_badge" => "Intermediate",
            "next_badge" => "Advanced",
            "remaining_to_unlock_next_badge" => 3
        ];
        $oneCommentStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
                4 => "50 Lessons Watched",
                5 => "First Comment Written",
            ],
            "next_available_achievements" => [
              0 => "3 Comments Written",
            ],
            "current_badge" => "Intermediate",
            "next_badge" => "Advanced",
            "remaining_to_unlock_next_badge" => 2
        ];
        $threeCommentsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
                4 => "50 Lessons Watched",
                5 => "First Comment Written",
                6 => '3 Comments Written',
            ],
            "next_available_achievements" => [
              0 => "5 Comments Written",
            ],
            "current_badge" => "Intermediate",
            "next_badge" => "Advanced",
            "remaining_to_unlock_next_badge" => 1
        ];
        $fiveCommentsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
                4 => "50 Lessons Watched",
                5 => "First Comment Written",
                6 => '3 Comments Written',
                7 => "5 Comments Written",
            ],
            "next_available_achievements" => [
              0 => "10 Comments Written",
            ],
            "current_badge" => "Advanced",
            "next_badge" => "Master",
            "remaining_to_unlock_next_badge" => 2
        ];
        $tenCommentsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
                4 => "50 Lessons Watched",
                5 => "First Comment Written",
                6 => '3 Comments Written',
                7 => "5 Comments Written",
                8 => "10 Comments Written",
            ],
            "next_available_achievements" => [
              0 => "20 Comments Written",
            ],
            "current_badge" => "Advanced",
            "next_badge" => "Master",
            "remaining_to_unlock_next_badge" => 1
        ];
        $twentyCommentsStatus = [
            "unlocked_achievements" => [
                0 => "First Lesson Watched",
                1 => "5 Lessons Watched",
                2 => "10 Lessons Watched",
                3 => "25 Lessons Watched",
                4 => "50 Lessons Watched",
                5 => "First Comment Written",
                6 => '3 Comments Written',
                7 => "5 Comments Written",
                8 => "10 Comments Written",
                9 => "20 Comments Written",
            ],
            "current_badge" => "Master",
        ];

        $counter = 1;
        $commentsAchievementsList = Comment::commentsWrittenAchievementsMap();

        while ($counter < 30)
        {
            foreach ($commentsAchievementsList as $amount => $label)
            {
                $userCommentsCounter = $user->comments()->count();

                if ($userCommentsCounter == 0)
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($zeroCommentsStatus);
                }
                if (($userCommentsCounter == $amount) && ($amount == 1))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($oneCommentStatus, $strict = false);
                }
                if (($userCommentsCounter > 1) && ($userCommentsCounter < 3) && ($amount == 1))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($oneCommentStatus, $strict = false);
                }
                if (($userCommentsCounter == $amount) && ($amount == 3))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($threeCommentsStatus, $strict = false);
                }
                if (($userCommentsCounter > 3) && ($userCommentsCounter < 5) && ($amount == 3))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($threeCommentsStatus, $strict = false);
                }
                if (($userCommentsCounter == $amount) && ($amount == 5))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($fiveCommentsStatus, $strict = false);
                }
                if (($userCommentsCounter > 5) && ($userCommentsCounter < 10) && ($amount == 5))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($fiveCommentsStatus, $strict = false);
                }
                if (($userCommentsCounter == $amount) && ($amount == 10))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($tenCommentsStatus, $strict = false);
                }
                if (($userCommentsCounter > 10) && ($userCommentsCounter < 20) && ($amount == 5))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($tenCommentsStatus, $strict = false);
                }
                if (($userCommentsCounter == $amount) && ($amount == 20))
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($twentyCommentsStatus, $strict = false);
                }
                if ($userCommentsCounter > 20)
                {
                    $response = $this->get("/users/{$user->id}/achievements");
                    $response->assertStatus(200);
                    $response->assertExactJson($twentyCommentsStatus, $strict = false);
                }

                $comment = new Comment;
                $comment->fill(['body' => 'Some comment', 'user_id' => $user->id]);
                $comment->save();
                $counter++;
            }
        }
    }
}

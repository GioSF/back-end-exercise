<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;

class AssignUserLessonsAccomplishments
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $lesson = $event->lesson;
        $user = $event->user;
        $unlockedAchievement = $lesson->getUnlockedAchievement($user);

        if($unlockedAchievement)
        {
            AchievementUnlocked::dispatch([
                'achievement_name' => $unlockedAchievement,
                'user' => $user]);
        };

        $unlockedBadge = $user->getUnlockedBadgeName();

        if($unlockedBadge && $unlockedAchievement)
        {
            BadgeUnlocked::dispatch([
                'badge_name' => $unlockedBadge,
                'user' => $user]);
        };
    }
}

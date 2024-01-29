<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use Illuminate\Support\Facades\Log;

class AssignUserCommentsAccomplishments
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
        $comment = $event->comment;
        $user = $comment->user;
        $unlockedAchievement = $comment->getUnlockedAchievement($user);

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

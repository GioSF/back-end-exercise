<?php

namespace App\Models;

use App\Models\LessonUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title'
    ];

    public function users(): BelongsToMany
	{
        return $this->belongsToMany(User::class)->using(LessonUser::class);
	}

    static public function lessonsWatchedAchievementsMap(): array
    {
        return [
            1 => 'First Lesson Watched',
            5 => '5 Lessons Watched',
            10 => '10 Lessons Watched',
            25 => '25 Lessons Watched',
            50 => '50 Lessons Watched',
        ];
    }

    public function getUnlockedAchievement(User $user)
    {
        $userLessonsCount = $user->lessons()->where('watched', true)->count();
        $achievementsList = self::lessonsWatchedAchievementsMap();
        $hasNewAchievement = array_key_exists($userLessonsCount, $achievementsList);
        $unlockedAchievementName = $hasNewAchievement ? $achievementsList[$userLessonsCount] : null;

        return $unlockedAchievementName;
    }

    static public function getUserLessonsWatchedAchievementsData(User $user): array
    {
        $lessonsWatched = $user->lessons()
            ->where('watched', true)
            ->count();
        $achivements = [];

        foreach(self::lessonsWatchedAchievementsMap() as $requiredAmount => $achievementLabel)
        {
            if($lessonsWatched >= $requiredAmount)
            {
                $achivements['unlocked_achievements'][] = $achievementLabel;
            }
            else
            {
                $achivements['next_available_achievements'][] = $achievementLabel;
                break;
            };
        }

        return $achivements;
    }

}

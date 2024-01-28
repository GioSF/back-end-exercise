<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'body',
        'user_id'
    ];

    /**
     * Get the user that wrote the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    static public function commentsWrittenAchievementsMap(): array
    {
        return [
            1 => 'First Comment Written',
            3 => '3 Comments Written',
            5 => '5 Comments Written',
            10 => '10 Comments Written',
            20 => '20 Comments Written',
        ]; 
    }

    public function getUnlockedAchievement(User $user)
    {
        $userCommentsCount = $this->user->comments()->count();
        $achievementsList = self::commentsWrittenAchievementsMap();
        $hasNewAchievement = array_key_exists($userCommentsCount, $achievementsList);
        $unlockedAchievementName = $hasNewAchievement ? $achievementsList[$userCommentsCount] : null;

        return $unlockedAchievementName;
    }

    static public function getUserCommentsWrittenAchievementsData(User $user): array
    {
        $commentsWritten = $user->comments()->count();
        $achivements = [];

        foreach(self::commentsWrittenAchievementsMap() as $requiredAmount => $achievementLabel)
        {
            if($commentsWritten >= $requiredAmount)
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

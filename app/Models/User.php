<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\LessonUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    private $userAchievementsData;

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    public function getUserEntitiesAchievementsData(): array
    {
        $commentAchievements = Comment::getUserCommentsWrittenAchievementsData($this);
        $lessonAchievements = Lesson::getUserLessonsWatchedAchievementsData($this);

        return array_merge_recursive(
            $lessonAchievements,
            $commentAchievements,
        );
    }

    public function getUserBadgesMap(): array
    {
        return [
            0 => 'Beginner',
            4 => 'Intermediate',
            8 => 'Advanced',
            10 => 'Master',
        ];
    }

    public function getUserAccomplishmentsData(): array
    {
        $userAchievementsCount = $this->getUserAchievementsCount();
        $userBadgesData = [];

        foreach ($this->getUserBadgesMap() as $requiredAmount => $badgeLabel)
        {
            if ($userAchievementsCount >= $requiredAmount)
            {
                $userBadgesData['current_badge'] = $badgeLabel;
            }
            else
            {
                $userBadgesData['next_badge'] = $badgeLabel;
                $userBadgesData['remaining_to_unlock_next_badge'] = $requiredAmount - $userAchievementsCount;
                break;
            }
        }

        return array_merge_recursive(
            $this->userAchievementsData,
            $userBadgesData,
        );
    }

    public function getUserAchievementsCount(): int
    {
        $this->userAchievementsData = $this->getUserEntitiesAchievementsData();
        $isUnlockedAchievementsSet = isset($this->userAchievementsData['unlocked_achievements']);
        $userAchievementsCount = $isUnlockedAchievementsSet ? count($this->userAchievementsData['unlocked_achievements']) : 0;

        return $userAchievementsCount;
    }

    public function getUnlockedBadgeName()
    {
        $userAchievementsCount = $this->getUserAchievementsCount();
        $badgesList = $this->getUserBadgesMap();
        $hasNewBadge = array_key_exists($userAchievementsCount, $badgesList);
        $unlockedBadgeName = $hasNewBadge ? $badgesList[$userAchievementsCount] : null;

        return $unlockedBadgeName;
    }

}

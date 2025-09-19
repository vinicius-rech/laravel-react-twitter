<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'visibility',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisibleTo($query, $user = null)
    {
        if(!$user) {
            return $query->where('visibility', 'public');
        }

        return $query->where(function($visibilityFilter) use ($user) {
            $visibilityFilter->where('visibility', 'public')
                ->orWhere(function($userOwnItems) use ($user) {
                    $userOwnItems->where('visibility', 'private')
                        ->where('user_id', $user->id);
            });
        });
    }
}

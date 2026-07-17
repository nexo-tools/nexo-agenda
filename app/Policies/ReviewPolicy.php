<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function moderate(User $user, Review $review): bool
    {
        return $review->business->user_id === $user->id;
    }
}

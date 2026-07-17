<?php

namespace App\Policies;

use App\Models\Professional;
use App\Models\User;

class ProfessionalPolicy
{
    public function update(User $user, Professional $professional): bool
    {
        return $professional->business->user_id === $user->id;
    }

    public function delete(User $user, Professional $professional): bool
    {
        return $this->update($user, $professional);
    }
}

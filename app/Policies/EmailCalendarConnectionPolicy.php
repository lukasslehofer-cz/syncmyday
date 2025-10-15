<?php

namespace App\Policies;

use App\Models\EmailCalendarConnection;
use App\Models\User;

class EmailCalendarConnectionPolicy
{
    /**
     * Determine if the user can view the email calendar connection.
     */
    public function view(User $user, EmailCalendarConnection $emailCalendar): bool
    {
        return $user->id === $emailCalendar->user_id;
    }

    /**
     * Determine if the user can update the email calendar connection.
     */
    public function update(User $user, EmailCalendarConnection $emailCalendar): bool
    {
        return $user->id === $emailCalendar->user_id;
    }

    /**
     * Determine if the user can delete the email calendar connection.
     */
    public function delete(User $user, EmailCalendarConnection $emailCalendar): bool
    {
        return $user->id === $emailCalendar->user_id;
    }
}


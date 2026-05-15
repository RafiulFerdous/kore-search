<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function created(User $user): void
    {
        Cache::increment('admin.users.version');
    }

    public function updated(User $user): void
    {
        Cache::increment('admin.users.version');
    }

    public function deleted(User $user): void
    {
        Cache::increment('admin.users.version');
    }
}

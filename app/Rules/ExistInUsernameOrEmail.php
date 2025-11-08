<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class ExistInUsernameOrEmail implements Rule
{
    public function passes($attribute, $value): bool
    {
        return User::where('email', $value)
            ->orWhere('name', $value)
            ->exists();
    }

    public function message(): string
    {
        return 'No user found with this email or name.';
    }
}


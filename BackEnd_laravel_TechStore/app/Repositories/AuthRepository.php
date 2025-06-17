<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository
{
    public function create($data)
    {
        return User::create($data);
    }

    public function findByEmail($email)
    {
        return User::where('email',$email)->first();
    }

    public function firstOrCreateByEmail($email, $name, $hashedPass)
    {
        return User::firstOrCreate(['email' => $email], [
            'name' => $name,
            'password' => $hashedPass
        ]);
    }
}

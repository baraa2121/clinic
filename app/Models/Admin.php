<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Passport\HasApiTokens as PassportHasApiTokens;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements MustVerifyEmail

{
    use PassportHasApiTokens, HasRoles;
    protected string $guard_name = 'admin';


    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Find the user instance for the given username.
     */

    public function findForPassport(string $username): Admin
    {
        return $this->where('email', $username)->first();
    }

    public function validateForPassportPasswordGrant(string $password): bool
    {
        return Hash::check($password, $this->password);
    }
}

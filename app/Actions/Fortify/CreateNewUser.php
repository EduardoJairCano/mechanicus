<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     * @return User
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        // Validate information.
        Validator::make($input, [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'cell_phone_number' => ['required', 'integer'],
            'password'          => $this->passwordRules(),
            'terms'             => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        // Create new user resource.
        $user = User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
            'role_id'  => 3,
        ]);

        if ($user) {
            // Create new user_info resource.
            UserInfo::create([
                'cell_phone_number' => $input['cell_phone_number'],
                'user_id'           => $user->id,
            ]);
        }

        return $user;
    }
}

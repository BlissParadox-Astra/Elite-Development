<?php

namespace App\Managers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManager
{
    public function getAllUsers($page, $itemsPerPage)
    {
        return User::with(['userType', 'userCredential'])->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function createUser(array $userData, array $credentialData)
    {
        $user = User::create($userData);

        $user->userCredential()->create([
            'username' => $credentialData['username'],
            'password' => Hash::make($credentialData['password']),
        ]);

        return $user;
    }

    public function getUserByIdWithRelations($id)
    {
        return User::with(['userType', 'userCredential'])->findOrFail($id);
    }
    public function updateUser(User $user, array $data)
    {
        $user->update([
            'user_type_id' => $data['user_type_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'age' => $data['age'],
            'address' => $data['address'],
            'contact_number' => $data['contact_number'],
        ]);

        if (isset($data['username'])) {
            $user->userCredential->update([
                'username' => $data['username'],
            ]);
        }

        if (!empty($data['password'])) {
            $user->userCredential->update([
                'password' => Hash::make($data['password']),
            ]);
        }
    }

    public function deleteUserWithCredentials(User $user): void
    {
        if ($user->userCredential) {
            $user->userCredential->delete();
        }

        $user->delete();
    }
}

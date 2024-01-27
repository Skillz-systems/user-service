<?php

namespace App\Service;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * Class UserService
 *
 * Service class responsible for user-related operations.
 *
 * @package App\Service
 */

class UserService
{

    /**
     * Create a new user.
     *
     * This method validates the provided user data and creates a new user in the database.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     *
     * The $request parameter should contain the following keys:
     *   - 'email' (string, required): The email address of the new user.
     *   - 'name' (string, required): The name of the new user.
     *   - 'password' (string, required): The password for the new user. Should be at least 8 characters long.
     *
     * @throws \Illuminate\Validation\ValidationException Thrown if validation fails.
     *
     * @return \App\Models\User|string Returns the created user object if successful, otherwise a string with the error message.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        $user = new User($request->all());
        $user->save();

        return $user;
    }

    /**
     * Update user credentials in the database.
     *
     * This method allows updating the user's email, name, and password based on the provided data.
     *
     * @param int $userId The ID of the user whose credentials are to be updated.
     * @param array $userData The array containing the updated user data.
     *
     * The $userData parameter may contain the following keys:
     *   - 'email' (string, optional): The new email address for the user. Should be a valid email format.
     *   - 'name' (string, optional): The new name for the user. Should be a string with a maximum length of 255 characters.
     *   - 'password' (string, optional): The new password for the user. Should be at least 8 characters long.
     *
     * @return bool|array Returns true if the update is successful. If validation fails, it returns an array of validation errors.
     *                   If the specified user ID is not found or if the new email already exists for another user, it returns false.
     */
    public function updateUserCredentials(int $userId, array $userData) : bool|array
    {
        // Validate the provided user data
        $validator = Validator::make($userData, [
            'email' => 'sometimes|email|unique:users',
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // Check if the new email is different and exists in the database
        if (array_key_exists('email', $userData) && $userData['email'] !== $user->email && User::where('email', $userData['email'])->exists()) {
            return false;
        }

        $user->update($userData);

        return $user;
    }
}

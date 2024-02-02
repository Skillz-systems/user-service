<?php

namespace App\Service;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @return bool|array|\App\Models\User Returns true if the update is successful.
     *                   If validation fails, it returns an array of validation errors.
     *                   If the specified user ID is not found or if the new email already exists for another user, it returns false.
     *                   If the update is successful, it returns the updated user object.
     */
    public function updateUserCredentials(int $userId, array $userData): bool|array|User
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

        $user->update($userData);

        return $user;
    }

    /**
     * Get a paginated list of users for a specific page.
     *
     * @param int $page The page number.
     * @param int $perPage The number of users to display per page.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsersForPage(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return User::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Assign a user to a particular department.
     *
     * @param int $userId       The ID of the user.
     * @param int $departmentId The ID of the department.
     *
     * @return bool Returns true on success, false on failure.
     */
    public function assignUserToDepartment(int $userId, int $departmentId): bool
    {
        // Validate user and department IDs
        $validator = Validator::make([
            'user_id' => $userId,
            'department_id' => $departmentId,
        ], [
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return false;
        }

        $user = User::find($userId);

        $user->department()->associate($departmentId);

        $user->save();

        return true;
    }
}

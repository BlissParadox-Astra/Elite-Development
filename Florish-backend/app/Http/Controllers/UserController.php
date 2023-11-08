<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Managers\UserManager;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $itemsPerPage = $request->input('itemsPerPage', 10);

            $users = $this->userManager->getAllUsers($page, $itemsPerPage);
            
            return response()->json([
                'users' => $users->items(),
                'totalItems' => $users->total(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = $this->userManager->createUser(
                [
                    'user_type_id' => $validatedData['user_type_id'],
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'gender' => $validatedData['gender'],
                    'age' => $validatedData['age'],
                    'address' => $validatedData['address'],
                    'contact_number' => $validatedData['contact_number'],
                ],
                [
                    'username' => $validatedData['username'],
                    'password' => $validatedData['password'],
                ]
            );

            if ($user) {
                return response()->json(['status' => 200, 'message' => 'User registered successfully'], 200);
            } else {
                return response()->json(['status' => 500, 'message' => 'Something went Wrong'], 200);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->userManager->getUserByIdWithRelations($id);

            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            $this->userManager->updateUser($user, $request->validated());

            return response()->json(['message' => 'User updated successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            $this->userManager->deleteUserWithCredentials($user);

            return response()->json(['message' => 'User and associated credentials deleted successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Show user types in user type field in User Form.
     */
    public function getUserTypes()
    {
        try {
            $userTypes = UserType::all();

            return response()->json($userTypes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch user types.'], 500);
        }
    }
}

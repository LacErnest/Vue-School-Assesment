<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'asc');

        $users = $this->userService->getUsersWithPagination($limit, $offset, [], ['*'], $sort, $direction);
        
        return response()->json($users);
    }

    public function show($id)
    {
        $user = $this->userService->getUser($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json($user);
    }

    public function store(UserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json($user, 201);
    }

    public function update(UserRequest $request, $id)
    {
        $user = $this->userService->getUser($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $updated = $this->userService->updateUser($id, $request->validated());
        
        if ($updated) {
            return response()->json($this->userService->getUser($id));
        }
        
        return response()->json(['message' => 'Failed to update user'], 500);
    }

    public function destroy($id)
    {
        $user = $this->userService->getUser($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $deleted = $this->userService->deleteUser($id);
        
        if ($deleted) {
            return response()->json(null, 204);
        }
        
        return response()->json(['message' => 'Failed to delete user'], 500);
    }
}
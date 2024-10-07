<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUser($id): ?User
    {
        return $this->userRepository->firstByIdOrNull($id);
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function updateUser($id, array $data): bool
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser($id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function getAllUsers(array $with = [], array $columns = ['*']): Collection
    {
        return $this->userRepository->getAll($with, $columns);
    }

    public function getUsersWithPagination(int $limit = 10, int $offset = 0, array $with = [], array $columns = ['*'], string $sort = 'created_at', string $direction = 'asc'): Collection
    {
        return $this->userRepository->getAllWithTrashed($limit, $offset, $with, $columns, $sort, $direction);
    }
}
<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll()
    {
        return $this->userRepository->getAll();
    }

    public function getById($id)
    {
        return $this->userRepository->getById($id);
    }

    public function create($request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->remember_token = Hash::make($request->remember_token);
        $user->save();
        return $user;
    }

    public function update($id, $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->userRepository->delete($id);
    }
}

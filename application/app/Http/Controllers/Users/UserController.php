<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    //method index
    public function index() {
        return $this->userService->getAll();
    }

    //method store
    public function store(UserRequest $request) {
        return $this->userService->create($request);
    }

    //method show
    public function show(string $id) {
        return $this->userService->getById($id);
    }

    //method update
    public function update(string $id, UserRequest $request) {
        return $this->userService->update($id, $request);
    }

    //method destroy
    public function destroy(string $id) {
        return $this->userService->delete($id);
    }
}

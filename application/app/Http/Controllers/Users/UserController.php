<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
    public function store(Request $request) {

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'remember' => 'required'
        ]);

        return $this->userService->create($request);
    }

    //method show
    public function show(string $id) {
        return $this->userService->getById($id);
    }

    //method update
    public function update(string $id, Request $request) {
        return $this->userService->update($id, $request);
    }

    //method destroy
    public function destroy(string $id) {
        return $this->userService->delete($id);
    }
}

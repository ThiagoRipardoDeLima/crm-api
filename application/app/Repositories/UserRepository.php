<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * @var User
     */
    protected $users;

    public function __construct(User $model)
    {
        $this->users = new User();
    }

    public function getAll()
    {
        return $this->users->all();
    }

    public function getById($id)
    {
        return $this->users->findOrFail($id);
    }

    public function create(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->remember_token = Hash::make($request->remember_token);
        $user->save();

        return response()->json($user, 201);
    }

    public function update($id, $attributes)
    {
        $user = $this->users->find($id);
        if($user === null)
            return response()->json(['success' => false, 'detail' => 'Registro não encontrado'], 404);

        $rules = $this->users->rules();
        if($attributes->method() === 'PATCH')
            $rules = array_intersect_key($user->rules(), $attributes->all());

        $attributes->validate($rules, $user->feedback());

        $user->fill($attributes->all());

        $user->save();

        return response()->json($user, 200);
    }

    public function delete($id)
    {
        return $this->users->find($id)->delete();
    }
}

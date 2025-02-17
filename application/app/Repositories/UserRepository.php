<?php
namespace App\Repositories;

use App\Models\User;

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
        return $this->users->find($id);
    }

    public function create(array $attributes)
    {
        return $this->users->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->users->find($id)->update($attributes);
    }

    public function delete($id)
    {
        return $this->users->find($id)->delete();
    }
}

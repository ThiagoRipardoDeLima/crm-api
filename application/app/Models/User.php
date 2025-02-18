<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rules(): array
    {
        return  [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users'.$this->id,
            'password' => 'required|min:6',
            'remember' => 'required'
        ];
    }

    public function Messages()
    {
        return [
            'required' => 'O campo :attribute é obrigratório',
            'email.email' => 'O campo :attribute é invalido',
            'email.unique' => 'O :attribute já está em uso',
            'password.min' => 'O campo :attribute de conter pelo menos :min caracteres',
            'remember.required' => 'O campo :attribute é obrigatório',
            'remember.boolean' => 'O campo :attribute deve ser verdadeiro ou falso'
        ];
    }
}

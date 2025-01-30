<?php
namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Neo4jUser implements Authenticatable
{
    use AuthenticatableTrait;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->data['id'];
    }

    public function getAuthPassword()
    {
        // If you're storing passwords, return the hashed password.
        return $this->data['password'] ?? ''; // Example
    }

    public function getRememberToken()
    {
        return $this->data['remember_token'] ?? null;
    }

    public function setRememberToken($value)
    {
        $this->data['remember_token'] = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}

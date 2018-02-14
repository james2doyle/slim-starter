<?php

namespace Application\Models;

use Latitude\QueryBuilder\Conditions;

/**
 * The user class
 */
class User extends BaseModel
{
    protected $table = 'users';
    protected $primary_key = 'id';
    protected $hidden = ['password'];

    public function findByEmail(string $email)
    {
        return $this->select()
            ->where(Conditions::make('email = ?', $email));
    }
}

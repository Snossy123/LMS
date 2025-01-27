<?php

namespace App\Repositories;

use App\Interfaces\userRepositoryInterface;
use App\Models\Neo4jUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class userRepository implements userRepositoryInterface
{
    private $dbsession;
    public function __construct()
    {
        $client = app('neo4j');
        $this->dbsession = $client->createSession();
    }

    public function attempt(array $credentials):bool
    {
        $result = $this->dbsession->run(
            'MATCH (p:Person {email: $email}) RETURN labels(p) as labels, p',
            ['email' => $credentials['email']]
        );

        if ($result->count() > 0) {
            $personNode = $result->first()->get('p');
            $labels = $result->first()->get('labels')->toArray();
            $personPass = $personNode->getProperties()->get('password');
            if(password_verify($credentials['password'], $personPass)){
                $user_type = in_array('Admin', $labels) ? 'Admin' : (
                    in_array('Teacher', $labels) ? 'Teacher' : 'Student'
                );
                $data = [
                    'id' => $personNode->getId(),
                    'name' => $personNode->getProperties()->get('name'),
                    'email' => $personNode->getProperties()->get('email'),
                    'type' => $user_type,
                ];
                $logedInUser = new Neo4jUser($data);
                Auth::login($logedInUser);
                return true;
            }
            throw new AuthenticationException("Invalid password");
        }

        throw new AuthenticationException("Invalid credentials");
    }
}

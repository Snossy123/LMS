<?php
namespace App\Providers;

use App\Models\Neo4jUser;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class Neo4jUserProvider implements UserProvider
{
    protected $neo4j;

    public function __construct()
    {
        $client = app('neo4j');
        $this->neo4j = $client->createSession();
    }

    public function retrieveById($identifier)
    {
        $result = $this->neo4j->run(
            'MATCH (p:Person) WHERE ID(p) = $id RETURN p, labels(p) as labels',
            ['id' => $identifier]
        );

        if ($result->count() > 0) {
            $personNode = $result->first()->get('p');
            $labels = $result->first()->get('labels')->toArray();
            $user_type = in_array('Admin', $labels) ? 'Admin' : (
                in_array('Teacher', $labels) ? 'Teacher' : 'Student'
            );
            $data = [
                'id' => $personNode->getId(),
                'name' => $personNode->getProperties()->get('name'),
                'email' => $personNode->getProperties()->get('email'),
                'type' => $user_type,
            ];
            return new Neo4jUser($data);
        }

        return null;
    }

    public function retrieveByCredentials(array $credentials)
    {
        $result = $this->neo4j->run(
            'MATCH (p:Person {email: $email}) RETURN p, labels(p) as labels',
            ['email' => $credentials['email']]
        );

        if ($result->count() > 0) {
            $personNode = $result->first()->get('p');
            $labels = $result->first()->get('labels')->toArray();
            $user_type = in_array('Admin', $labels) ? 'Admin' : (
                in_array('Teacher', $labels) ? 'Teacher' : 'Student'
            );
            $data = [
                'id' => $personNode->getId(),
                'name' => $personNode->getProperties()->get('name'),
                'email' => $personNode->getProperties()->get('email'),
                'type' => $user_type,
            ];
            return new Neo4jUser($data);
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return password_verify($credentials['password'], $user->getAuthPassword());
    }

    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}
}

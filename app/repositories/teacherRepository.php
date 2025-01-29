<?php

namespace App\Repositories;

use App\Http\Requests\teacherCreationRequest;
use App\Http\Requests\teacherUpdateRequest;
use App\Interfaces\teacherRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class teacherRepository implements teacherRepositoryInterface
{
    protected $dbsession;
    public function __construct()
    {
        $this->dbsession = app('neo4j')->createSession();
    }
    public function addTeacher(teacherCreationRequest $request): int
    {
        try {
            // Run a query to create a node
            $query = 'create (t:Person:Teacher {name:$name, email:$email, password:$password, specialty:$specialty, about:$about, image:$imageURL}) return t';

            // Define the parameters
            $imageURL = $this->getImgUrl($request);
            $params = [
                "name" => $request->teacher_name,
                "email" => $request->teacher_email,
                "password" => bcrypt($request->teacher_password),
                "specialty" => $request->teacher_specialty,
                "about" => $request->teacher_about,
                "imageURL" => $imageURL,
            ];
            // Execute the query and get the result
            $result = $this->dbsession->run($query, $params);

            $teacher = $result->first()->get('t');

            return $teacher->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while creating Teacher Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while Creating Teacher Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function getTeacher(int $teacherId): array
    {
        try {
            $query = 'MATCH (t:Teacher) WHERE ID(t) = $teacherID RETURN t';
            $result = $this->dbsession->run($query, ['teacherID' => $teacherId]);
            $teacherNode = $result->first()->get('t');
            $teacherProperties = $teacherNode->getProperties()->toArray();
            $teacher = [
                'id' => $teacherNode->getId(),
                'name' => $teacherProperties['name'] ?? null,
                'email' => $teacherProperties['email'] ?? null,
                'specialty' => $teacherProperties['specialty'] ?? null,
                'about' => $teacherProperties['about'] ?? null,
                'imageURL' => $teacherProperties['image'] ?? null,
            ];
            return $teacher;
        } catch (QueryException $e) {
            Log::error("Database Error while retrive Teacher Node: {$e->getMessage()}", ["teacher ID" => $teacherId]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrive Teacher Node: {$e->getMessage()}", ["teacher ID" => $teacherId]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $paginate = 10;
        $skip = ($page - 1) * $paginate;

        $query = 'MATCH (t:Teacher)
                  WITH count(t) AS totalTeachers
                  MATCH (t:Teacher)
                  SKIP $skip LIMIT $paginate
                  RETURN collect({id:ID(t), name:t.name, email:t.email, specialty:t.specialty}) AS teachers, totalTeachers';

        $result = $this->dbsession->run($query, [
            'skip' => $skip,
            'paginate' => $paginate,
        ]);

        $record = $result->first();
        $teachers = $record->get('teachers')->toArray();
        $totalTeachers = $record->get('totalTeachers');

        $paginatedTeachers = new \Illuminate\Pagination\LengthAwarePaginator(
            $teachers,
            $totalTeachers,
            $paginate,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginatedTeachers;
    }

    public function getImgUrl(teacherCreationRequest|teacherUpdateRequest $request)
    {
        if ($request->hasFile('teacher_img')) {
            $file = $request->file('teacher_img');
            $uniqueFileName = uniqid() . $this->getSlugAttribute($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('teachers/images'), $uniqueFileName);
            $newFileURL = asset('teachers/images/' . $uniqueFileName);
            return $newFileURL;
        }
    }

    public function getSlugAttribute(string $title): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }

    public function editTeacher(teacherUpdateRequest $request)
    {
        try {
            // Run a query to update a node
            $query = '
                    MATCH (t:Teacher)
                    WHERE ID(t) = $id
                    SET t += {
                        name: $name,
                        email: $email,
                        password: $password,
                        specialty: $specialty,
                        about: $about,
                        image: $imageURL
                    }
                    RETURN t
                ';

            // Check if an image URL is provided, otherwise use the previous image URL
            $imageURL = $request->has('teacher_img') ? $this->getImgUrl($request) : $request->query->get('prev_img');

            // Define the parameters for the query
            $params = [
                "id" => (int) $request->query->get('teacher_id'),
                "name" => $request->teacher_name,
                "email" => $request->teacher_email,
                "password" => $request->teacher_password,
                "specialty" => $request->teacher_specialty,
                "about" => $request->teacher_about,
                "imageURL" => $imageURL
            ];

            // Execute the query
            $result = $this->dbsession->run($query, $params);
            $teacher = $result->first()->get('t');

            return $teacher->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while update Teacher Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while update Teacher Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function deleteTeacher(Request $request)
    {
        try {
            // Run a query to update a node
            $query = 'MATCH (t:Teacher) WHERE ID(t) = $id DETACH DELETE t';

            // Define the parameters for the query
            $params = ["id" => (int) $request->query->get('teacher_id')];

            // Execute the query
            $this->dbsession->run($query, $params);
        } catch (QueryException $e) {
            Log::error("Database Error while update Teacher Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while update Teacher Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function getTeachers()
    {
        $query = 'MATCH (t:Teacher)
                  RETURN collect({id:ID(t), name:t.name, email:t.email, specialty:t.specialty}) AS teachers';

        $result = $this->dbsession->run($query);

        $record = $result->first();
        $teachers = $record->get('teachers')->toArray();
        return $teachers;
    }
}

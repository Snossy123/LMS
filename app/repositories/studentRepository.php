<?php

namespace App\Repositories;

use App\Http\Requests\studentCreationRequest;
use App\Http\Requests\studentUpdateRequest;
use App\Interfaces\studentRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class studentRepository implements studentRepositoryInterface
{
    protected $dbsession;
    public function __construct()
    {
        $this->dbsession = app('neo4j')->createSession();
    }
    public function addStudent(studentCreationRequest $request): int
    {
        try {
            // Run a query to create a node
            $query = 'create (t:Person:Student {name:$name, email:$email, password:$password, specialty:$specialty, about:$about, image:$imageURL}) return t';

            // Define the parameters
            $imageURL = $this->getImgUrl($request);
            $params = [
                "name" => $request->student_name,
                "email" => $request->student_email,
                "password" => bcrypt($request->student_password),
                "specialty" => $request->student_specialty,
                "about" => $request->student_about,
                "imageURL" => $imageURL,
            ];
            // Execute the query and get the result
            $result = $this->dbsession->run($query, $params);

            $student = $result->first()->get('t');

            return $student->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while creating Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while Creating Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function getStudent(int $studentId): array
    {
        try {
            $query = 'MATCH (t:Student) WHERE ID(t) = $studentID RETURN t';
            $result = $this->dbsession->run($query, ['studentID' => $studentId]);
            $studentNode = $result->first()->get('t');
            $studentProperties = $studentNode->getProperties()->toArray();
            $student = [
                'id' => $studentNode->getId(),
                'name' => $studentProperties['name'] ?? null,
                'email' => $studentProperties['email'] ?? null,
                'specialty' => $studentProperties['specialty'] ?? null,
                'about' => $studentProperties['about'] ?? null,
                'imageURL' => $studentProperties['image'] ?? null,
            ];
            return $student;
        } catch (QueryException $e) {
            Log::error("Database Error while retrive Student Node: {$e->getMessage()}", ["student ID" => $studentId]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrive Student Node: {$e->getMessage()}", ["student ID" => $studentId]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function index(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $paginate = 10;
            $skip = ($page - 1) * $paginate;

            $query = 'MATCH (t:Student)
                  WITH count(t) AS totalStudents
                  MATCH (t:Student)
                  SKIP $skip LIMIT $paginate
                  RETURN collect({id:ID(t), image:t.image, name:t.name, email:t.email, specialty:t.specialty}) AS students, totalStudents';

            $result = $this->dbsession->run($query, [
                'skip' => $skip,
                'paginate' => $paginate,
            ]);
            $students=[]; $totalStudents=0;
            if($result->count()){
                $record = $result->first();
                $students = $record->get('students')->toArray();
                $totalStudents = $record->get('totalStudents');
            }
            $paginatedStudents = new \Illuminate\Pagination\LengthAwarePaginator(
                $students,
                $totalStudents,
                $paginate,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return $paginatedStudents;
        } catch (QueryException $e) {
            Log::error("Database Error while retrive Student Node: {$e->getMessage()}");
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrive Student Node: {$e->getMessage()}");
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function getImgUrl(studentCreationRequest|studentUpdateRequest $request)
    {
        if ($request->hasFile('student_img')) {
            $file = $request->file('student_img');
            $uniqueFileName = uniqid() . $this->getSlugAttribute($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('students/images'), $uniqueFileName);
            $newFileURL = asset('students/images/' . $uniqueFileName);
            return $newFileURL;
        }
    }

    public function getSlugAttribute(string $title): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }

    public function editStudent(studentUpdateRequest $request)
    {
        try {
            // Run a query to update a node
            $query = '
                    MATCH (t:Student)
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
            $imageURL = $request->has('student_img') ? $this->getImgUrl($request) : $request->query->get('prev_img');

            // Define the parameters for the query
            $params = [
                "id" => (int) $request->query->get('student_id'),
                "name" => $request->student_name,
                "email" => $request->student_email,
                "password" => $request->student_password,
                "specialty" => $request->student_specialty,
                "about" => $request->student_about,
                "imageURL" => $imageURL
            ];

            // Execute the query
            $result = $this->dbsession->run($query, $params);
            $student = $result->first()->get('t');

            return $student->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while update Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while update Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function deleteStudent(Request $request)
    {
        try {
            // To delete nodes and any relationships connected them, use the DETACH DELETE clause.
            $query = 'MATCH (t:Student) WHERE id(t) = $id DETACH DELETE t';
            $params = ["id" => (int) $request->query('student_id')];
            $this->dbsession->run($query, $params);
        } catch (QueryException $e) {
            Log::error("Database Error while delete Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while delete Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function enrollInCourse(Request $request)
    {
        try{
            // Define the query to create a relationship between the student and course node
            $query = 'MATCH (s:Student) WHERE ID(s) = $student_id
                      MATCH (c:Course) WHERE ID(c) = $course_id
                      CREATE (s)-[r:ENROLL_IN {enroll_date:datetime()}]->(c)';
            $params = [
            "course_id" => (int) $request->query('course_id'),
            "student_id" => (int) Auth::user()->data['id']
            ];
            $this->dbsession->run($query, $params); 
        } catch (QueryException $e) {
            Log::error("Database Error while enroll Student in course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while enroll Student in course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }
}

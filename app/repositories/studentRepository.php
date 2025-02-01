<?php

namespace App\Repositories;

use App\Http\Requests\StudentCreationRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Interfaces\StudentRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentRepository implements StudentRepositoryInterface
{
    protected $dbsession;

    /**
     * Constructor to initialize the Neo4j database session.
     */
    public function __construct()
    {
        $this->dbsession = app('neo4j')->createSession();
    }

    // ==================== STUDENT CREATION METHODS ====================

    /**
     * Add a new student to the database.
     *
     * @param StudentCreationRequest $request
     * @return int
     * @throws \Exception
     */
    public function addStudent(StudentCreationRequest $request): int
    {
        try {
            $query = '
                CREATE (t:Person:Student {
                    name: $name,
                    email: $email,
                    password: $password,
                    specialty: $specialty,
                    about: $about,
                    image: $imageURL
                })
                RETURN t
            ';

            $imageURL = $this->getImgUrl($request);
            $params = [
                "name" => $request->student_name,
                "email" => $request->student_email,
                "password" => bcrypt($request->student_password),
                "specialty" => $request->student_specialty,
                "about" => $request->student_about,
                "imageURL" => $imageURL,
            ];

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

    // ==================== STUDENT RETRIEVAL METHODS ====================

    /**
     * Get a student by their ID.
     *
     * @param int $studentId
     * @return array
     * @throws \Exception
     */
    public function getStudent(int $studentId): array
    {
        try {
            $query = 'MATCH (t:Student) WHERE ID(t) = $studentID RETURN t';
            $result = $this->dbsession->run($query, ['studentID' => $studentId]);
            $studentNode = $result->first()->get('t');
            $studentProperties = $studentNode->getProperties()->toArray();

            return [
                'id' => $studentNode->getId(),
                'name' => $studentProperties['name'] ?? null,
                'email' => $studentProperties['email'] ?? null,
                'specialty' => $studentProperties['specialty'] ?? null,
                'about' => $studentProperties['about'] ?? null,
                'imageURL' => $studentProperties['image'] ?? null,
            ];
        } catch (QueryException $e) {
            Log::error("Database Error while retrieving Student Node: {$e->getMessage()}", ["student ID" => $studentId]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrieving Student Node: {$e->getMessage()}", ["student ID" => $studentId]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    /**
     * Get a paginated list of all students.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $paginate = 10;
            $skip = ($page - 1) * $paginate;

            $query = '
                MATCH (t:Student)
                WITH count(t) AS totalStudents
                MATCH (t:Student)
                SKIP $skip LIMIT $paginate
                RETURN collect({
                    id: ID(t),
                    image: t.image,
                    name: t.name,
                    email: t.email,
                    specialty: t.specialty
                }) AS students, totalStudents
            ';

            $result = $this->dbsession->run($query, [
                'skip' => $skip,
                'paginate' => $paginate,
            ]);

            $students = [];
            $totalStudents = 0;

            if ($result->count()) {
                $record = $result->first();
                $students = $record->get('students')->toArray();
                $totalStudents = $record->get('totalStudents');
            }

            return new \Illuminate\Pagination\LengthAwarePaginator(
                $students,
                $totalStudents,
                $paginate,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } catch (QueryException $e) {
            Log::error("Database Error while retrieving Student Nodes: {$e->getMessage()}");
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrieving Student Nodes: {$e->getMessage()}");
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    // ==================== STUDENT UPDATE METHODS ====================

    /**
     * Update an existing student.
     *
     * @param StudentUpdateRequest $request
     * @return int
     * @throws \Exception
     */
    public function editStudent(StudentUpdateRequest $request): int
    {
        try {
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

            $imageURL = $request->has('student_img') ? $this->getImgUrl($request) : $request->query->get('prev_img');
            $params = [
                "id" => (int) $request->query->get('student_id'),
                "name" => $request->student_name,
                "email" => $request->student_email,
                "password" => bcrypt($request->student_password),
                "specialty" => $request->student_specialty,
                "about" => $request->student_about,
                "imageURL" => $imageURL,
            ];

            $result = $this->dbsession->run($query, $params);
            $student = $result->first()->get('t');

            return $student->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while updating Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while updating Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    // ==================== STUDENT DELETION METHODS ====================

    /**
     * Delete a student by their ID.
     *
     * @param Request $request
     * @throws \Exception
     */
    public function deleteStudent(Request $request)
    {
        try {
            $query = 'MATCH (t:Student) WHERE ID(t) = $id DETACH DELETE t';
            $params = ["id" => (int) $request->query('student_id')];
            $this->dbsession->run($query, $params);
        } catch (QueryException $e) {
            Log::error("Database Error while deleting Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while deleting Student Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    // ==================== COURSE ENROLLMENT METHODS ====================

    /**
     * Enroll a student in a course.
     *
     * @param Request $request
     * @throws \Exception
     */
    public function enrollInCourse(Request $request)
    {
        try {
            $query = '
                MATCH (s:Student) WHERE ID(s) = $student_id
                MATCH (c:Course) WHERE ID(c) = $course_id
                CREATE (s)-[r:ENROLL_IN {enroll_date: datetime()}]->(c)
            ';

            $params = [
                "course_id" => (int) $request->query('course_id'),
                "student_id" => (int) Auth::user()->data['id'],
            ];

            $this->dbsession->run($query, $params);
        } catch (QueryException $e) {
            Log::error("Database Error while enrolling Student in Course: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while enrolling Student in Course: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Generate a URL for the student image.
     *
     * @param StudentCreationRequest|StudentUpdateRequest $request
     * @return string|null
     */
    private function getImgUrl(StudentCreationRequest|StudentUpdateRequest $request): ?string
    {
        if ($request->hasFile('student_img')) {
            $file = $request->file('student_img');
            $uniqueFileName = uniqid() . $this->getSlugAttribute($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('students/images'), $uniqueFileName);
            return 'students/images/' . $uniqueFileName;
        }
        return null;
    }

    /**
     * Generate a slug from a string.
     *
     * @param string $title
     * @return string
     */
    private function getSlugAttribute(string $title): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }
}

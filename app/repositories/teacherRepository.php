<?php

namespace App\Repositories;

use App\Http\Requests\TeacherCreationRequest;
use App\Http\Requests\TeacherUpdateRequest;
use App\Interfaces\TeacherRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeacherRepository implements TeacherRepositoryInterface
{
    protected $dbsession;

    /**
     * Initialize Neo4j database session
     */
    public function __construct()
    {
        $this->dbsession = app('neo4j')->createSession();
    }

    // ==================== TEACHER CREATION METHODS ====================

    /**
     * Create a new teacher node
     *
     * @param TeacherCreationRequest $request
     * @return int
     * @throws \Exception
     */
    public function addTeacher(TeacherCreationRequest $request): int
    {
        try {
            $query = '
                CREATE (t:Person:Teacher {
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
                "name" => $request->teacher_name,
                "email" => $request->teacher_email,
                "password" => bcrypt($request->teacher_password),
                "specialty" => $request->teacher_specialty,
                "about" => $request->teacher_about,
                "imageURL" => $imageURL,
            ];

            $result = $this->dbsession->run($query, $params);
            return $result->first()->get('t')->getId();
        } catch (\Throwable $e) {
            Log::error("Database error creating teacher: {$e->getMessage()}", $request->all());
            throw new \Exception("Could not create teacher. Please try again.");
        }
    }

    // ==================== TEACHER RETRIEVAL METHODS ====================

    /**
     * Get teacher details by ID
     *
     * @param int $teacherId
     * @return array
     * @throws \Exception
     */
    public function getTeacher(int $teacherId): array
    {
        try {
            $query = 'MATCH (t:Teacher) WHERE ID(t) = $teacherID RETURN t';
            $result = $this->dbsession->run($query, ['teacherID' => $teacherId]);
            $teacherNode = $result->first()->get('t');
            $properties = $teacherNode->getProperties()->toArray();

            return [
                'id' => $teacherNode->getId(),
                'name' => $properties['name'] ?? null,
                'email' => $properties['email'] ?? null,
                'specialty' => $properties['specialty'] ?? null,
                'about' => $properties['about'] ?? null,
                'imageURL' => $properties['image'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error("Error retrieving teacher {$teacherId}: {$e->getMessage()}");
            throw new \Exception("Could not retrieve teacher information.");
        }
    }

    /**
     * Get paginated list of teachers
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $paginate = 10;
            $skip = ($page - 1) * $paginate;

            $query = '
                MATCH (t:Teacher)
                WITH count(t) AS totalTeachers
                MATCH (t:Teacher)
                SKIP $skip LIMIT $paginate
                RETURN collect({
                    id: ID(t),
                    name: t.name,
                    email: t.email,
                    specialty: t.specialty
                }) AS teachers,
                totalTeachers
            ';

            $result = $this->dbsession->run($query, [
                'skip' => $skip,
                'paginate' => $paginate,
            ]);

            $record = $result->first();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $record->get('teachers')->toArray(),
                $record->get('totalTeachers'),
                $paginate,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } catch (\Throwable $e) {
            Log::error("Error listing teachers: {$e->getMessage()}");
            throw new \Exception("Could not retrieve teacher list.");
        }
    }

    // ==================== TEACHER UPDATE METHODS ====================

    /**
     * Update teacher information
     *
     * @param TeacherUpdateRequest $request
     * @return int
     * @throws \Exception
     */
    public function editTeacher(TeacherUpdateRequest $request): int
    {
        try {
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

            $imageURL = $request->has('teacher_img')
                ? $this->getImgUrl($request)
                : $request->query->get('prev_img');

            $params = [
                "id" => (int) $request->query->get('teacher_id'),
                "name" => $request->teacher_name,
                "email" => $request->teacher_email,
                "password" => bcrypt($request->teacher_password),
                "specialty" => $request->teacher_specialty,
                "about" => $request->teacher_about,
                "imageURL" => $imageURL
            ];

            $result = $this->dbsession->run($query, $params);
            return $result->first()->get('t')->getId();
        } catch (\Throwable $e) {
            Log::error("Error updating teacher: {$e->getMessage()}", $request->all());
            throw new \Exception("Could not update teacher information.");
        }
    }

    // ==================== TEACHER DELETION METHODS ====================

    /**
     * Delete a teacher node
     *
     * @param Request $request
     * @throws \Exception
     */
    public function deleteTeacher(Request $request)
    {
        try {
            $this->dbsession->run(
                'MATCH (t:Teacher) WHERE ID(t) = $id DETACH DELETE t',
                ["id" => (int) $request->query->get('teacher_id')]
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting teacher: {$e->getMessage()}", $request->all());
            throw new \Exception("Could not delete teacher.");
        }
    }

    // ==================== REPORTING METHODS ====================

    /**
     * Get teacher report data with course and student information
     *
     * @return mixed
     * @throws \Exception
     */
    public function reportData()
    {
        try {
            $teacherId = (int) Auth::user()->data['id'];

            $query = '
                MATCH (t:Teacher)-[:TEACH]->(c:Course)
                WHERE ID(t) = $teacher_id
                OPTIONAL MATCH (c)<-[:ENROLL_IN]-(s:Student)
                WITH t, c, count(s) AS totalStudents, collect(s {.*, id: ID(s)}) AS students
                WITH t, collect({
                    title: c.title,
                    category: c.category,
                    level: c.level,
                    totalStudents: totalStudents,
                    students: students
                }) AS courses
                RETURN {
                    id: ID(t),
                    name: t.name,
                    specialty: t.specialty,
                    image: t.image,
                    totalCourses: size(courses),
                    totalStudents: reduce(total = 0, course IN courses | total + course.totalStudents),
                    courses: courses
                } AS teacher
            ';

            $result = $this->dbsession->run($query, ['teacher_id' => $teacherId]);

            if ($result->count() === 0) {
                throw new \Exception('No teacher data found');
            }

            return $result->first()->get('teacher');
        } catch (\Throwable $e) {
            Log::error("Error generating teacher report: {$e->getMessage()}");
            throw new \Exception("Could not generate teacher report.");
        }
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Generate image URL from uploaded file
     *
     * @param TeacherCreationRequest|TeacherUpdateRequest $request
     * @return string|null
     */
    private function getImgUrl(TeacherCreationRequest|TeacherUpdateRequest $request): ?string
    {
        if ($request->hasFile('teacher_img')) {
            $file = $request->file('teacher_img');
            $fileName = uniqid() . $this->getSlugAttribute($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('teachers/images'), $fileName);
            return asset('teachers/images/' . $fileName);
        }
        return null;
    }

    /**
     * Generate URL-safe slug from string
     *
     * @param string $title
     * @return string
     */
    private function getSlugAttribute(string $title): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }

    /**
     * Get all teachers (non-paginated)
     *
     * @return array
     */
    public function getTeachers(): array
    {
        try {
            $result = $this->dbsession->run('
                MATCH (t:Teacher)
                RETURN collect({id: ID(t), name: t.name, email: t.email, specialty: t.specialty}) AS teachers
            ');

            return $result->first()->get('teachers')->toArray();
        } catch (\Throwable $e) {
            Log::error("Error retrieving teachers: {$e->getMessage()}");
            return [];
        }
    }
}

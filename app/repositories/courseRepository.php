<?php

namespace App\Repositories;

use App\Http\Requests\CourseCreationRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseRepository implements CourseRepositoryInterface
{
    protected $dbsession;

    /**
     * Constructor to initialize the Neo4j database session.
     */
    public function __construct()
    {
        $this->dbsession = app('neo4j')->createSession();
    }

    // ==================== COURSE CREATION METHODS ====================

    /**
     * Add a new course to the database.
     *
     * @param CourseCreationRequest $request
     * @return int
     * @throws \Exception
     */
    public function addCourse(CourseCreationRequest $request): int
    {
        try {
            $query = '
                MERGE (c:Course {title: $title, category: $category, level: $level, language: $language, description: $description, details: $details, image: $imageURL})
                WITH c
                MATCH (t:Teacher)
                WHERE ID(t) = $teacher_id
                CREATE (t)-[r:TEACH]->(c)
                RETURN c
            ';

            $imageURL = $this->getImgUrl($request);
            $params = [
                "title" => $request->course_title,
                "category" => $request->course_category,
                "level" => $request->course_level,
                "language" => $request->course_language,
                "description" => $request->course_description,
                "details" => $request->course_details,
                "imageURL" => $imageURL,
                "teacher_id" => (int) $request->course_teacher,
            ];

            $result = $this->dbsession->run($query, $params);
            $course = $result->first()->get('c');
            return $course->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while creating Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while Creating Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    // ==================== COURSE RETRIEVAL METHODS ====================

    /**
     * Get a course by its ID.
     *
     * @param int $courseId
     * @return array
     * @throws \Exception
     */
    public function getCourse(int $courseId): array
    {
        try {
            $query = '
                MATCH (c:Course) WHERE ID(c) = $courseID
                OPTIONAL MATCH (c)<-[r:TEACH]-(t:Teacher)
                RETURN {
                    id: ID(c),
                    title: c.title,
                    category: c.category,
                    level: c.level,
                    language: c.language,
                    imageURL: c.image,
                    details: c.details,
                    description: c.description,
                    teacher_id: CASE WHEN t IS NOT NULL THEN ID(t) ELSE NULL END,
                    teacher_name: CASE WHEN t IS NOT NULL THEN t.name ELSE NULL END
                } AS course
            ';

            $result = $this->dbsession->run($query, ['courseID' => $courseId]);
            return $result->first()->get('course')->toArray();
        } catch (QueryException $e) {
            Log::error("Database Error while retrieving Course Node: {$e->getMessage()}", ["course ID" => $courseId]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrieving Course Node: {$e->getMessage()}", ["course ID" => $courseId]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    /**
     * Get a paginated list of all courses.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $paginate = 10;
        $skip = ($page - 1) * $paginate;

        $query = '
            MATCH (c:Course)
            WITH count(c) AS totalCourses
            MATCH (c:Course)
            SKIP $skip LIMIT $paginate
            OPTIONAL MATCH (c)<-[r:TEACH]-(t:Teacher)
            RETURN
                COLLECT({
                    id: ID(c),
                    title: c.title,
                    category: c.category,
                    level: c.level,
                    language: c.language,
                    imageURL: c.image,
                    details: c.details,
                    description: c.description,
                    teacher_id: CASE WHEN t IS NOT NULL THEN ID(t) ELSE NULL END,
                    teacher_name: CASE WHEN t IS NOT NULL THEN t.name ELSE NULL END
                }) AS courses,
                totalCourses
        ';

        $result = $this->dbsession->run($query, [
            'skip' => $skip,
            'paginate' => $paginate,
        ]);

        $record = $result->first();
        $courses = $record->get('courses')->toArray();
        $totalCourses = $record->get('totalCourses');

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $courses,
            $totalCourses,
            $paginate,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    // ==================== COURSE UPDATE METHODS ====================

    /**
     * Update an existing course.
     *
     * @param CourseUpdateRequest $request
     * @return int
     * @throws \Exception
     */
    public function editCourse(CourseUpdateRequest $request): int
    {
        try {
            $query = '
                MATCH (c:Course)
                WHERE ID(c) = $id
                SET c += {
                    title: $title,
                    category: $category,
                    level: $level,
                    language: $language,
                    description: $description,
                    details: $details,
                    image: $imageURL
                }
                RETURN c
            ';

            $imageURL = $request->has('course_img') ? $this->getImgUrl($request) : $request->query->get('prev_img');
            $params = [
                "id" => (int) $request->query->get('course_id'),
                "title" => $request->course_title,
                "category" => $request->course_category,
                "level" => $request->course_level,
                "language" => $request->course_language,
                "description" => $request->course_description,
                "details" => $request->course_details,
                "imageURL" => $imageURL,
            ];

            $result = $this->dbsession->run($query, $params);
            $course = $result->first()->get('c');

            // Update teacher relationship if changed
            $prevTeacher = (int) $request->query('prev_teacher');
            $newTeacher = (int) $request->course_teacher;

            if ($prevTeacher !== $newTeacher) {
                $query = '
                    MATCH (t:Teacher) WHERE ID(t) = $prev_teacher
                    MATCH (c:Course) WHERE ID(c) = $course_id
                    MATCH (t)-[r:TEACH]->(c)
                    DELETE r
                    WITH c, t
                    MATCH (new_t:Teacher) WHERE ID(new_t) = $new_teacher
                    CREATE (new_t)-[r_new:TEACH]->(c)
                ';

                $params = [
                    "prev_teacher" => $prevTeacher,
                    "course_id" => $course->getId(),
                    "new_teacher" => $newTeacher,
                ];

                $this->dbsession->run($query, $params);
            }

            return $course->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while updating Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while updating Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    // ==================== COURSE DELETION METHODS ====================

    /**
     * Delete a course by its ID.
     *
     * @param Request $request
     * @throws \Exception
     */
    public function deleteCourse(Request $request)
    {
        try {
            $query = 'MATCH (c:Course) WHERE ID(c) = $id DETACH DELETE c';
            $params = ["id" => (int) $request->query->get('course_id')];
            $this->dbsession->run($query, $params);
        } catch (QueryException $e) {
            Log::error("Database Error while deleting Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while deleting Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    // ==================== STUDENT COURSE METHODS ====================

    /**
     * Get a paginated list of courses enrolled by a student.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function studentCourses(Request $request)
    {
        $page = $request->query('page', 1);
        $paginate = 10;
        $skip = ($page - 1) * $paginate;

        $query = '
            MATCH (s:Student)-[:ENROLL_IN]->(c:Course)
            WHERE ID(s) = $student_id
            WITH c, count(c) AS totalCourses
            SKIP $skip LIMIT $paginate
            OPTIONAL MATCH (c)<-[r:TEACH]-(t:Teacher)
            RETURN
                COLLECT({
                    id: ID(c),
                    title: c.title,
                    category: c.category,
                    level: c.level,
                    language: c.language,
                    imageURL: c.image,
                    details: c.details,
                    description: c.description,
                    teacher_id: CASE WHEN t IS NOT NULL THEN ID(t) ELSE NULL END,
                    teacher_name: CASE WHEN t IS NOT NULL THEN t.name ELSE NULL END
                }) AS courses,
                totalCourses
        ';

        $result = $this->dbsession->run($query, [
            'skip' => $skip,
            'paginate' => $paginate,
            'student_id' => Auth::user()->data['id'],
        ]);

        $record = $result->first();
        $courses = $record->get('courses')->toArray();
        $totalCourses = $record->get('totalCourses');

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $courses,
            $totalCourses,
            $paginate,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    /**
     * Check if a student is enrolled in a specific course.
     *
     * @param Request $request
     * @return bool
     */
    public function checkStudentEnroll(Request $request): bool
    {
        $query = '
            OPTIONAL MATCH (s:Student)-[:ENROLL_IN]->(c:Course)
            WHERE ID(s) = $student_id AND ID(c) = $course_id
            RETURN c IS NOT NULL AS enrolled
        ';

        $result = $this->dbsession->run($query, [
            'student_id' => (int) Auth::user()->data['id'],
            'course_id' => (int) $request->query('course_id'),
        ]);

        return $result->first()->get('enrolled');
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Generate a URL for the course image.
     *
     * @param CourseCreationRequest|CourseUpdateRequest $request
     * @return string|null
     */
    private function getImgUrl(CourseCreationRequest|CourseUpdateRequest $request): ?string
    {
        if ($request->hasFile('course_img')) {
            $file = $request->file('course_img');
            $uniqueFileName = uniqid() . $this->getSlugAttribute($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('courses/images'), $uniqueFileName);
            return asset('courses/images/' . $uniqueFileName);
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

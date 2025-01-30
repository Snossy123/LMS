<?php

namespace App\Repositories;

use App\Http\Requests\courseCreationRequest;
use App\Http\Requests\courseUpdateRequest;
use App\Interfaces\courseRepositoryInterface;
use App\Models\Neo4jUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laudis\Neo4j\Formatter\BasicFormatter;

class courseRepository implements courseRepositoryInterface
{
    protected $dbsession;
    public function __construct()
    {
        $this->dbsession = app('neo4j')->createSession();
    }
    public function addCourse(courseCreationRequest $request): int
    {
        try {
            // Run a query to create a node
            $query = 'MERGE (c:Course {title:$title, category:$category, level:$level, language:$language, description:$description, details:$details, image:$imageURL})
            with c
                    MATCH (t:Teacher)
                    WHERE ID(t) = $teacher_id
                    CREATE (t)-[r:TEACH]->(c)
                    return c';

            // Define the parameters
            $imageURL = $this->getImgUrl($request);
            $params = [
                "title" => $request->course_title,
                "category" => $request->course_category,
                "level" => $request->course_level,
                "language" => $request->course_language,
                "description" => $request->course_description,
                "details" => $request->course_details,
                "imageURL" => $imageURL,
                "teacher_id" => (int) $request->course_teacher
            ];

            // Execute the query and get the result
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

    public function getCourse(int $courseId): array
    {
        try {
            $query = 'MATCH (c:Course) WHERE ID(c) = $courseID
                    OPTIONAL MATCH (c)<-[r:TEACH]-(t:Teacher)
                    RETURN {
                    id:ID(c),
                    title:c.title,
                    category:c.category,
                    level:c.level,
                    language:c.language,
                    imageURL:c.image,
                    details:c.details,
                    description:c.description,
                    teacher_id: CASE WHEN t IS NOT NULL THEN ID(t) ELSE NULL END,
                    teacher_name:CASE WHEN t IS NOT NULL THEN t.name ELSE NULL END
                    }AS course';
            $result = $this->dbsession->run($query, ['courseID' => $courseId]);
            $courseNode = $result->first()->get('course')->toArray();
            return $courseNode;
        } catch (QueryException $e) {
            Log::error("Database Error while retrive Course Node: {$e->getMessage()}", ["course ID" => $courseId]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrive Course Node: {$e->getMessage()}", ["course ID" => $courseId]);
            dd($e->getMessage());
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $paginate = 10;
        $skip = ($page - 1) * $paginate;

        $query = 'MATCH (c:Course)
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
                    totalCourses';

        $result = $this->dbsession->run($query, [
            'skip' => $skip,
            'paginate' => $paginate,
        ]);

        $record = $result->first();
        $courses = $record->get('courses')->toArray();
        $totalCourses = $record->get('totalCourses');

        $paginatedCourses = new \Illuminate\Pagination\LengthAwarePaginator(
            $courses,
            $totalCourses,
            $paginate,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginatedCourses;
    }

    public function getImgUrl(courseCreationRequest|courseUpdateRequest $request)
    {
        if ($request->hasFile('course_img')) {
            $file = $request->file('course_img');
            $uniqueFileName = uniqid() . $this->getSlugAttribute($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('courses/images'), $uniqueFileName);
            $newFileURL = asset('courses/images/' . $uniqueFileName);
            return $newFileURL;
        }
    }

    public function getSlugAttribute(string $title): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }

    public function editCourse(courseUpdateRequest $request)
    {
        try {
            // Run a query to update a node
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

            // Check if an image URL is provided, otherwise use the previous image URL
            $imageURL = $request->has('course_img') ? $this->getImgUrl($request) : $request->query->get('prev_img');

            // Define the parameters for the query
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

            // Execute the query
            $result = $this->dbsession->run($query, $params);
            $course = $result->first()->get('c');


            $prev_teacher = (int)$request->query('prev_teacher');
            $new_teacher = (int)$request->course_teacher;
            // if teacher change need to drop relationship and create new relationship
            if($prev_teacher !== $new_teacher){
                $query = 'MATCH (t:Teacher) WHERE ID(t)=$prev_teacher
                        MATCH (c:Course) WHERE ID(c)=$course_id
                        MATCH (t)-[r:TEACH]->(c)
                        DELETE r
                        WITH c,t
                        MATCH (new_t:Teacher) WHERE ID(new_t)=$new_teacher
                        CREATE (new_t)-[r_new:TEACH]->(c)';
                $params = [
                    "prev_teacher" => $prev_teacher,
                    "course_id" => $course->getId(),
                    "new_teacher" => $new_teacher
                ];
                $this->dbsession->run($query, $params);
            }

            return $course->getId();
        } catch (QueryException $e) {
            Log::error("Database Error while update Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while update Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function deleteCourse(Request $request)
    {
        try {
            // Run a query to update a node
            $query = 'MATCH (c:Course) WHERE ID(c) = $id DETACH DELETE c';

            // Define the parameters for the query
            $params = ["id" => (int) $request->query->get('course_id')];

            // Execute the query
            $this->dbsession->run($query, $params);
        } catch (QueryException $e) {
            Log::error("Database Error while update Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while update Course Node: {$e->getMessage()}", ["request" => $request->all()]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function studentCourses(Request $request)
    {
        $page = $request->query('page', 1);
        $paginate = 10;
        $skip = ($page - 1) * $paginate;

        $query = 'MATCH (s:Student)-[:ENROLL_IN]->(c:Course)
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
                    totalCourses;';

        $result = $this->dbsession->run($query, [
            'skip' => $skip,
            'paginate' => $paginate,
            'student_id' => Auth::user()->data['id']
        ]);

        $record = $result->first();
        $courses = $record->get('courses')->toArray();
        $totalCourses = $record->get('totalCourses');

        $paginatedCourses = new \Illuminate\Pagination\LengthAwarePaginator(
            $courses,
            $totalCourses,
            $paginate,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginatedCourses;
    }

    public function checkStudentEnroll(Request $request)
    {
        $query = 'OPTIONAL MATCH (s:Student)-[:ENROLL_IN]->(c:Course)
                  WHERE ID(s) = $student_id AND ID(c) = $course_id
                  RETURN c IS NOT NULL AS enrolled';

        $result = $this->dbsession->run($query, [
            'student_id' => Auth::user()->data['id'],
            'course_id' => (int) $request->query('course_id')
        ]);

        $record = $result->first();
        $enrolled = $record->get('enrolled');

        return $enrolled;
    }

}

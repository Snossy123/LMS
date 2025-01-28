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
            $query = 'create (c:Course {title:$title, category:$category, level:$level, language:$language, description:$description, details:$details, image:$imageURL}) return c';

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
            $query = 'MATCH (c:Course) WHERE ID(c) = $courseID RETURN c';
            $result = $this->dbsession->run($query, ['courseID' => $courseId]);
            $courseNode = $result->first()->get('c');
            $courseProperties = $courseNode->getProperties()->toArray();
            $course = [
                'id' => $courseNode->getId(),
                'title' => $courseProperties['title'] ?? null,
                'category' => $courseProperties['category'] ?? null,
                'level' => $courseProperties['level'] ?? null,
                'language' => $courseProperties['language'] ?? null,
                'description' => $courseProperties['description'] ?? null,
                'details' => $courseProperties['details'] ?? null,
                'imageURL' => $courseProperties['image'] ?? null,
            ];
            return $course;
        } catch (QueryException $e) {
            Log::error("Database Error while retrive Course Node: {$e->getMessage()}", ["course ID" => $courseId]);
            throw new \Exception("A Database Error occurred, Please try again later.");
        } catch (\Exception $e) {
            Log::error("Unexpected Error while retrive Course Node: {$e->getMessage()}", ["course ID" => $courseId]);
            throw new \Exception("An Unexpected Error occurred, please contact your support administrator.");
        }
    }

    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $paginate = 10;
        $skip = ($page - 1) * $paginate;

        $query = 'MATCH (c:Course)
                  WITH properties(c) AS props, id(c) AS id
                  SKIP $skip LIMIT $paginate
                  RETURN collect(apoc.map.merge(props, {id: id})) AS courses';

        $result = $this->dbsession->run($query, [
            'skip' => $skip,
            'paginate' => $paginate,
        ]);

        $courses = $result->first()->get('courses')->toArray();
        $totalCourses = $this->getTotalCoursesCount();

        $paginatedCourses = new \Illuminate\Pagination\LengthAwarePaginator(
            $courses,
            $totalCourses,
            $paginate,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginatedCourses;
    }

    public function getTotalCoursesCount(): int
    {
        try {
            // Cypher query to retrieve paginated courses
            $query = 'MATCH (c:Course)
                      RETURN count(c) AS totalCourses';

            // Execute query with parameters
            $result = $this->dbsession->run($query);
            return $result->first()->get('totalCourses');
        } catch (QueryException $e) {
            // Log query-specific errors and rethrow a user-friendly exception
            Log::error("Database Error while retrieving courses: {$e->getMessage()}");
            throw new \Exception("A database error occurred. Please try again later.");
        } catch (\Exception $e) {
            // Log unexpected errors and rethrow
            Log::error("Unexpected error while retrieving courses: {$e->getMessage()}");
            throw new \Exception("An unexpected error occurred. Please contact support.");
        }
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
                "id" => (int)$request->query->get('course_id'),
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
            $query = 'MATCH (c:Course) WHERE ID(c) = $id DELETE c';

            // Define the parameters for the query
            $params = [ "id" => (int)$request->query->get('course_id') ];

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
}

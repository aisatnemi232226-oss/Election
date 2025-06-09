<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
     public function index(Request $request)
    {
        $query = Course::orderBy('created_at', 'desc');

        // Handle search/filter
        if ($request->has('filter') && !empty($request->filter)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->filter . '%')
                  ->orWhere('coursecode', 'like', '%' . $request->filter . '%');
            });
        }

        $courses = $query->get();

        return view('admin.courses', compact('courses'));
    }
    
    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'coursecode' => 'required|string|max:255|unique:courses,coursecode',
            'name' => 'required|string|max:255',
        ]);

        try {
            Course::create([
                'coursecode' => strtoupper(trim($request->coursecode)),
                'name' => trim($request->name),
            ]);

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create course. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified course.
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course);
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'coursecode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'coursecode')->ignore($course->id),
            ],
            'name' => 'required|string|max:255',
        ]);

        try {
            $course->update([
                'coursecode' => strtoupper(trim($request->coursecode)),
                'name' => trim($request->name),
            ]);

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update course. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete(); // This will use soft delete

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete course. Please try again.');
        }
    }

    /**
     * Handle bulk deletion of courses.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        try {
            Course::whereIn('id', $request->course_ids)->delete();

            return redirect()->route('admin.courses.index')
                ->with('success', 'Selected courses deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete selected courses. Please try again.');
        }
    }

    /**
     * Get course data for AJAX requests.
     */
    public function getCourseData($id)
    {
        try {
            $course = Course::findOrFail($id);
            return response()->json([
                'success' => true,
                'course' => $course
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.'
            ], 404);
        }
    }
}
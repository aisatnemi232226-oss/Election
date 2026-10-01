<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'search_type' => ['nullable', 'in:all,name,email,idnumber'],
            'course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:255'],
            'verified' => ['nullable', 'in:verified,unverified'],
            'sort' => ['nullable', 'in:name,email,idnumber,course,year_level,verified'],
            'direction' => ['nullable', 'in:asc,desc'],
        ]);

        $courseFilter = $filters['course'] ?? '';
        $yearLevelFilter = $filters['year_level'] ?? '';
        $verifiedFilter = $filters['verified'] ?? '';
        $search = trim($filters['search'] ?? '');
        $searchType = $filters['search_type'] ?? 'all';
        $sort = $filters['sort'] ?? 'name';
        $sortColumns = [
            'name' => 'name',
            'email' => 'email',
            'idnumber' => 'idnumber',
            'course' => 'course',
            'year_level' => 'year_level',
            'verified' => 'verified_voter_exists',
        ];
        if (!array_key_exists($sort, $sortColumns)) {
            $sort = 'name';
        }
        $direction = $filters['direction'] ?? 'asc';

        $query = User::query()
            ->where('is_admin', false)
            ->withExists('verifiedVoter');

        if ($search !== '') {
            $searchColumns = $searchType === 'all'
                ? ['name', 'email', 'idnumber']
                : [$searchType];

            $query->where(function ($searchQuery) use ($searchColumns, $search) {
                foreach ($searchColumns as $column) {
                    $searchQuery->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        if ($courseFilter === '__none__') {
            $query->where(function ($courseQuery) {
                $courseQuery->whereNull('course')->orWhere('course', '');
            });
        } elseif ($courseFilter !== '') {
            $query->where('course', $courseFilter);
        }

        if ($yearLevelFilter === '__none__') {
            $query->where(function ($yearQuery) {
                $yearQuery->whereNull('year_level')->orWhere('year_level', '');
            });
        } elseif ($yearLevelFilter !== '') {
            $query->where('year_level', $yearLevelFilter);
        }

        if ($verifiedFilter === 'verified') {
            $query->whereHas('verifiedVoter');
        } elseif ($verifiedFilter === 'unverified') {
            $query->whereDoesntHave('verifiedVoter');
        }

        $users = $query
            ->orderBy($sortColumns[$sort], $direction)
            ->orderBy('id')
            ->paginate(20)
            ->appends($request->only('search', 'search_type', 'course', 'year_level', 'verified', 'sort', 'direction'));

        $courses = User::query()
            ->where('is_admin', false)
            ->whereNotNull('course')
            ->where('course', '<>', '')
            ->distinct()
            ->orderBy('course')
            ->pluck('course');
        $yearLevels = User::query()
            ->where('is_admin', false)
            ->whereNotNull('year_level')
            ->where('year_level', '<>', '')
            ->distinct()
            ->orderBy('year_level')
            ->pluck('year_level');

        return view('admin.users.index', compact(
            'users',
            'courses',
            'yearLevels',
            'search',
            'searchType',
            'courseFilter',
            'yearLevelFilter',
            'verifiedFilter',
            'sort',
            'direction'
        ));
    }
}

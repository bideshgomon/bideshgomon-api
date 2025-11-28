<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Service;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    /**
     * Get search suggestions based on query
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('query', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [
                    'popular' => $this->getPopularSearches(),
                    'users' => [],
                    'blog' => [],
                ]
            ]);
        }

        // Cache key based on query
        $cacheKey = 'search_suggestions_' . md5($query);

        $suggestions = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query) {
            return [
                'popular' => $this->getPopularSearches(),
                'users' => $this->searchUsers($query, 5),
                'blog' => $this->searchBlog($query, 5),
                'services' => $this->searchServices($query, 5),
            ];
        });

        return response()->json([
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Perform full search across all models
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');
        $filters = $request->input('filters', []);
        
        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'total' => 0
            ]);
        }

        $results = [
            'users' => $this->searchUsers($query, 10, $filters),
            'jobs' => $this->searchJobs($query, 10, $filters),
            'services' => $this->searchServices($query, 10, $filters),
            'blog' => $this->searchBlog($query, 10, $filters),
        ];

        $total = collect($results)->sum(fn($items) => count($items));

        return response()->json([
            'results' => $results,
            'total' => $total,
            'query' => $query
        ]);
    }

    /**
     * Search users
     */
    private function searchUsers(string $query, int $limit = 10, array $filters = [])
    {
        $builder = User::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%");

        if (!empty($filters['role'])) {
            $builder->whereHas('role', fn($q) => $q->where('slug', $filters['role']));
        }

        return $builder->limit($limit)
            ->get(['id', 'name', 'email', 'phone'])
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'type' => 'user',
                'url' => route('admin.users.show', $user->id)
            ])
            ->toArray();
    }

    /**
     * Search jobs
     */
    private function searchJobs(string $query, int $limit = 10, array $filters = [])
    {
        if (!class_exists(Job::class)) {
            return [];
        }

        $builder = Job::query()
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%");

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        return $builder->limit($limit)
            ->get(['id', 'title', 'location', 'salary_min', 'salary_max'])
            ->map(fn($job) => [
                'id' => $job->id,
                'title' => $job->title,
                'location' => $job->location ?? '',
                'salary' => $job->salary_min && $job->salary_max 
                    ? "৳{$job->salary_min} - ৳{$job->salary_max}"
                    : 'Not specified',
                'type' => 'job',
                'url' => route('admin.jobs.show', $job->id)
            ])
            ->toArray();
    }

    /**
     * Search services
     */
    private function searchServices(string $query, int $limit = 10, array $filters = [])
    {
        if (!class_exists(Service::class)) {
            return [];
        }

        $builder = Service::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%");

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        return $builder->limit($limit)
            ->get(['id', 'name', 'description', 'status'])
            ->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => substr($service->description ?? '', 0, 100),
                'status' => $service->status,
                'type' => 'service',
                'url' => route('admin.services.show', $service->id)
            ])
            ->toArray();
    }

    /**
     * Search blog posts
     */
    private function searchBlog(string $query, int $limit = 10, array $filters = [])
    {
        if (!class_exists(BlogPost::class)) {
            return [];
        }

        $builder = BlogPost::query()
            ->where('title', 'like', "%{$query}%")
            ->orWhere('excerpt', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%");

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        return $builder->limit($limit)
            ->get(['id', 'title', 'excerpt', 'slug'])
            ->map(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'type' => 'blog',
                'url' => route('admin.blog.posts.show', $post->id)
            ])
            ->toArray();
    }

    /**
     * Get popular search terms
     */
    private function getPopularSearches(): array
    {
        return Cache::remember('popular_searches', now()->addHours(24), function () {
            return [
                'Tourist Visa',
                'Work Permit',
                'Job Applications',
                'Medical Visa',
                'Education Visa',
            ];
        });
    }
}

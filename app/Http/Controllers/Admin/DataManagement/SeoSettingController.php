<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeoSettingController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = SeoSetting::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('page_type', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('keywords', 'LIKE', "%{$search}%");
            });
        }

        // Filter by index status
        if ($request->has('index') && $request->index !== '') {
            $query->where('index', $request->index);
        }

        // Filter by follow status
        if ($request->has('follow') && $request->follow !== '') {
            $query->where('follow', $request->follow);
        }

        // Sort
        $sortField = $request->get('sort', 'page_type');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $seoSettings = $query->paginate(20);

        return Inertia::render('Admin/DataManagement/SeoSettings/Index', [
            'seoSettings' => $seoSettings,
            'filters' => $request->only(['search', 'index', 'follow', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DataManagement/SeoSettings/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_type' => 'required|string|max:255|unique:seo_settings,page_type',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|url|max:255',
            'og_type' => 'nullable|string|max:50',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|url|max:255',
            'twitter_site' => 'nullable|string|max:100',
            'schema_markup' => 'nullable|json',
            'index' => 'boolean',
            'follow' => 'boolean',
            'robots' => 'nullable|string|max:255',
        ]);

        // Decode schema_markup JSON
        if (isset($validated['schema_markup'])) {
            $validated['schema_markup'] = json_decode($validated['schema_markup'], true);
        }

        SeoSetting::create($validated);

        return redirect()
            ->route('admin.data.seo-settings.index')
            ->with('success', 'SEO setting created successfully.');
    }

    public function edit(SeoSetting $seoSetting)
    {
        return Inertia::render('Admin/DataManagement/SeoSettings/Edit', [
            'seoSetting' => $seoSetting,
        ]);
    }

    public function update(Request $request, SeoSetting $seoSetting)
    {
        $validated = $request->validate([
            'page_type' => 'required|string|max:255|unique:seo_settings,page_type,' . $seoSetting->id,
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|url|max:255',
            'og_type' => 'nullable|string|max:50',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|url|max:255',
            'twitter_site' => 'nullable|string|max:100',
            'schema_markup' => 'nullable|json',
            'index' => 'boolean',
            'follow' => 'boolean',
            'robots' => 'nullable|string|max:255',
        ]);

        // Decode schema_markup JSON
        if (isset($validated['schema_markup'])) {
            $validated['schema_markup'] = json_decode($validated['schema_markup'], true);
        }

        $seoSetting->update($validated);

        return redirect()
            ->route('admin.data.seo-settings.index')
            ->with('success', 'SEO setting updated successfully.');
    }

    public function destroy(SeoSetting $seoSetting)
    {
        $seoSetting->delete();

        return redirect()
            ->route('admin.data.seo-settings.index')
            ->with('success', 'SEO setting deleted successfully.');
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return SeoSetting::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['page_type'];
    }

    protected function getOptionalColumns(): array
    {
        return [
            'title', 'description', 'keywords', 'canonical_url',
            'og_title', 'og_description', 'og_image', 'og_type',
            'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image', 'twitter_site',
            'schema_markup', 'index', 'follow', 'robots'
        ];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'page_type' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|url|max:255',
            'og_type' => 'nullable|string|max:50',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|url|max:255',
            'twitter_site' => 'nullable|string|max:100',
            'schema_markup' => 'nullable|string',
            'index' => 'nullable|boolean',
            'follow' => 'nullable|boolean',
            'robots' => 'nullable|string|max:255',
        ];
    }

    protected function saveRecord(array $data)
    {
        return SeoSetting::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'page_type' => 'Page type identifier (must be unique, e.g., home, about, contact)',
            'title' => 'Page meta title (60-70 characters recommended)',
            'description' => 'Meta description (150-160 characters recommended)',
            'keywords' => 'Meta keywords (comma-separated)',
            'canonical_url' => 'Canonical URL for the page',
            'og_title' => 'Open Graph title for social sharing',
            'og_description' => 'Open Graph description',
            'og_image' => 'Open Graph image URL (1200x630px recommended)',
            'og_type' => 'Open Graph type (website, article, etc.)',
            'twitter_card' => 'Twitter card type (summary_large_image, summary)',
            'twitter_title' => 'Twitter card title',
            'twitter_description' => 'Twitter card description',
            'twitter_image' => 'Twitter card image URL',
            'twitter_site' => 'Twitter site handle (@username)',
            'schema_markup' => 'JSON-LD schema markup',
            'index' => '1 to allow indexing, 0 to prevent',
            'follow' => '1 to follow links, 0 to nofollow',
            'robots' => 'Additional robots directives (e.g., noarchive, nosnippet)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'page_type' => 'home',
                'title' => 'Bidesh Gomon - Your Gateway to Overseas Opportunities',
                'description' => 'Find jobs abroad, apply for visas, and explore international opportunities with Bidesh Gomon - Bangladesh\'s leading overseas employment platform.',
                'keywords' => 'overseas jobs, visa applications, international employment, work abroad, Bangladesh',
                'canonical_url' => 'https://bideshgomon.com',
                'og_title' => 'Bidesh Gomon - Your Gateway to Overseas Opportunities',
                'og_description' => 'Find jobs abroad, apply for visas, and explore international opportunities.',
                'og_image' => 'https://bideshgomon.com/images/og-home.jpg',
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'twitter_title' => 'Bidesh Gomon - Overseas Opportunities',
                'twitter_description' => 'Find jobs abroad and visa services',
                'twitter_image' => 'https://bideshgomon.com/images/twitter-home.jpg',
                'twitter_site' => '@bideshgomon',
                'schema_markup' => '{"@context":"https://schema.org","@type":"Organization","name":"Bidesh Gomon"}',
                'index' => '1',
                'follow' => '1',
                'robots' => '',
            ],
            [
                'page_type' => 'jobs',
                'title' => 'Browse Overseas Jobs - Bidesh Gomon',
                'description' => 'Explore thousands of overseas job opportunities across various countries and industries. Apply for international jobs today.',
                'keywords' => 'overseas jobs, international jobs, foreign employment, job search',
                'canonical_url' => 'https://bideshgomon.com/jobs',
                'og_title' => 'Browse Overseas Jobs',
                'og_description' => 'Explore international job opportunities',
                'og_image' => 'https://bideshgomon.com/images/og-jobs.jpg',
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'twitter_title' => 'Overseas Jobs',
                'twitter_description' => 'Find your dream job abroad',
                'twitter_image' => 'https://bideshgomon.com/images/twitter-jobs.jpg',
                'twitter_site' => '@bideshgomon',
                'schema_markup' => '{"@context":"https://schema.org","@type":"JobPosting"}',
                'index' => '1',
                'follow' => '1',
                'robots' => '',
            ],
            [
                'page_type' => 'visa-services',
                'title' => 'Visa Application Services - Bidesh Gomon',
                'description' => 'Professional visa application assistance for work, tourist, and student visas. Expert guidance for successful visa applications.',
                'keywords' => 'visa services, visa application, work visa, tourist visa, student visa',
                'canonical_url' => 'https://bideshgomon.com/visa-services',
                'og_title' => 'Visa Application Services',
                'og_description' => 'Expert visa assistance for all visa types',
                'og_image' => 'https://bideshgomon.com/images/og-visa.jpg',
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'twitter_title' => 'Visa Services',
                'twitter_description' => 'Professional visa application help',
                'twitter_image' => 'https://bideshgomon.com/images/twitter-visa.jpg',
                'twitter_site' => '@bideshgomon',
                'schema_markup' => '{"@context":"https://schema.org","@type":"Service"}',
                'index' => '1',
                'follow' => '1',
                'robots' => '',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        if (SeoSetting::where('page_type', $row['page_type'])->exists()) {
            return "Row {$rowNumber}: SEO setting for page type '{$row['page_type']}' already exists";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        $data = [
            'page_type' => trim($row['page_type']),
            'title' => !empty($row['title']) ? trim($row['title']) : null,
            'description' => !empty($row['description']) ? trim($row['description']) : null,
            'keywords' => !empty($row['keywords']) ? trim($row['keywords']) : null,
            'canonical_url' => !empty($row['canonical_url']) ? trim($row['canonical_url']) : null,
            'og_title' => !empty($row['og_title']) ? trim($row['og_title']) : null,
            'og_description' => !empty($row['og_description']) ? trim($row['og_description']) : null,
            'og_image' => !empty($row['og_image']) ? trim($row['og_image']) : null,
            'og_type' => !empty($row['og_type']) ? trim($row['og_type']) : 'website',
            'twitter_card' => !empty($row['twitter_card']) ? trim($row['twitter_card']) : 'summary_large_image',
            'twitter_title' => !empty($row['twitter_title']) ? trim($row['twitter_title']) : null,
            'twitter_description' => !empty($row['twitter_description']) ? trim($row['twitter_description']) : null,
            'twitter_image' => !empty($row['twitter_image']) ? trim($row['twitter_image']) : null,
            'twitter_site' => !empty($row['twitter_site']) ? trim($row['twitter_site']) : null,
            'schema_markup' => !empty($row['schema_markup']) ? json_decode($row['schema_markup'], true) : null,
            'index' => isset($row['index']) ? (bool)$row['index'] : true,
            'follow' => isset($row['follow']) ? (bool)$row['follow'] : true,
            'robots' => !empty($row['robots']) ? trim($row['robots']) : null,
        ];

        return $data;
    }

    protected function getExportQuery(Request $request)
    {
        return SeoSetting::query()->orderBy('page_type');
    }

    protected function getExportColumns(): array
    {
        return [
            'page_type', 'title', 'description', 'keywords', 'canonical_url',
            'og_title', 'og_description', 'og_image', 'og_type',
            'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image', 'twitter_site',
            'schema_markup', 'index', 'follow', 'robots'
        ];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'page_type' => $model->page_type,
            'title' => $model->title ?? '',
            'description' => $model->description ?? '',
            'keywords' => $model->keywords ?? '',
            'canonical_url' => $model->canonical_url ?? '',
            'og_title' => $model->og_title ?? '',
            'og_description' => $model->og_description ?? '',
            'og_image' => $model->og_image ?? '',
            'og_type' => $model->og_type ?? 'website',
            'twitter_card' => $model->twitter_card ?? 'summary_large_image',
            'twitter_title' => $model->twitter_title ?? '',
            'twitter_description' => $model->twitter_description ?? '',
            'twitter_image' => $model->twitter_image ?? '',
            'twitter_site' => $model->twitter_site ?? '',
            'schema_markup' => !empty($model->schema_markup) ? json_encode($model->schema_markup) : '',
            'index' => $model->index ? '1' : '0',
            'follow' => $model->follow ? '1' : '0',
            'robots' => $model->robots ?? '',
        ];
    }
}

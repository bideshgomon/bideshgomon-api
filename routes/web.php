<?php

// --- CONTROLLER IMPORTS (All controllers) ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\JobCategoryPageController;
use App\Http\Controllers\Admin\JobPostingPageController;
use App\Http\Controllers\Admin\AirlinePageController;
use App\Http\Controllers\Admin\AirportPageController;
use App\Http\Controllers\Admin\FlightPageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TouristVisaPageController;
use App\Http\Controllers\PaymentController; // For SSLCommerz

// --- FACADES & MODELS ---
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Country;
use Illuminate\Http\Request;

// --- PUBLIC PAGES ---
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');
Route::get('/find-universities', [PublicPageController::class, 'universities'])->name('public.universities');
Route::get('/university/{university}', [PublicPageController::class, 'universityDetail'])->name('public.universities.show');
Route::get('/find-courses', [PublicPageController::class, 'courses'])->name('public.courses');
Route::get('/course/{course}', [PublicPageController::class, 'courseDetail'])->name('public.courses.show');
Route::get('/find-jobs', [PublicPageController::class, 'jobs'])->name('public.jobs');
Route::get('/job/{jobPosting}', [PublicPageController::class, 'jobDetail'])->name('public.job.detail');

// Travel Insurance Public Index Page
Route::get('/travel-insurance', function () {
    $countries = Country::orderBy('name')->select('id', 'name', 'code')->get();
    return Inertia::render('Public/TravelInsurance/Index', [
        'countries' => $countries
    ]);
})->name('public.travel-insurance.index');

// Travel Insurance Purchase Form Route (Requires Auth)
Route::get('/travel-insurance/purchase', function (Request $request) {
    $validatedData = $request->validate([
        'quote_reference' => 'required|string',
        'package_id' => 'required',
        'package_name' => 'required|string',
        'destination_country_id' => 'required|exists:countries,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'num_travelers' => 'required|integer|min:1',
        'total_premium' => 'required|numeric',
        'currency' => 'required|string',
    ]);
    $countries = Country::orderBy('name')->select('id', 'name', 'code')->get();
    return Inertia::render('Public/TravelInsurance/PurchaseForm', array_merge($validatedData, [
        'countries' => $countries,
    ]));
})->middleware(['auth', 'verified'])->name('public.travel-insurance.purchase');


// --- PAYMENT GATEWAY BACKEND CALLBACK ROUTES (SSLCOMMERZ) ---
// *** THESE ARE THE CORRECTED ROUTES ***
Route::post('/sslcommerz/success', [PaymentController::class, 'paymentSuccess'])->name('sslcommerz.success');
Route::post('/sslcommerz/fail', [PaymentController::class, 'paymentFail'])->name('sslcommerz.fail');
Route::post('/sslcommerz/cancel', [PaymentController::class, 'paymentCancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn', [PaymentController::class, 'paymentIpn'])->name('sslcommerz.ipn');
// --- END PAYMENT GATEWAY BACKEND ROUTES ---

// --- PAYMENT GATEWAY FRONTEND REDIRECT ROUTES ---
// These are for showing messages to the user *after* the callback
Route::get('/payment/success', fn (Request $request) => Inertia::render('Public/PaymentStatus/Success', ['query' => $request->query()]))->name('payment.frontend.success');
Route::get('/payment/fail', fn (Request $request) => Inertia::render('Public/PaymentStatus/Fail', ['query' => $request->query()]))->name('payment.frontend.fail');
Route::get('/payment/cancel', fn (Request $request) => Inertia::render('Public/PaymentStatus/Cancel', ['query' => $request->query()]))->name('payment.frontend.cancel');


// --- USER DASHBOARD & PROFILE ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');
});

// --- ADMIN PANEL ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => Redirect::route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Page Controllers
        Route::get('/universities', [UniversityPageController::class, 'index'])->name('universities.index');
        Route::get('/universities/create', [UniversityPageController::class, 'create'])->name('universities.create');
        Route::get('/universities/{university}/edit', [UniversityPageController::class, 'edit'])->name('universities.edit');

        Route::get('/courses', [CoursePageController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [CoursePageController::class, 'create'])->name('courses.create');
        Route::get('/courses/{course}/edit', [CoursePageController::class, 'edit'])->name('courses.edit');

        Route::get('/job-categories', [JobCategoryPageController::class, 'index'])->name('job-categories.index');
        Route::get('/job-categories/create', [JobCategoryPageController::class, 'create'])->name('job-categories.create');
        Route::get('/job-categories/{jobCategory}/edit', [JobCategoryPageController::class, 'edit'])->name('job-categories.edit');

        Route::get('/job-postings', [JobPostingPageController::class, 'index'])->name('job-postings.index');
        Route::get('/job-postings/create', [JobPostingPageController::class, 'create'])->name('job-postings.create');
        Route::get('/job-postings/{jobPosting}/edit', [JobPostingPageController::class, 'edit'])->name('job-postings.edit');

        Route::get('/airlines', [AirlinePageController::class, 'index'])->name('airlines.index');
        Route::get('/airlines/create', [AirlinePageController::class, 'create'])->name('airlines.create');
        Route::get('/airlines/{airline}/edit', [AirlinePageController::class, 'edit'])->name('airlines.edit');

        Route::get('/airports', [AirportPageController::class, 'index'])->name('airports.index');
        Route::get('/airports/create', [AirportPageController::class, 'create'])->name('airports.create');
        Route::get('/airports/{airport}/edit', [AirportPageController::class, 'edit'])->name('airports.edit');

        Route::get('/flights', [FlightPageController::class, 'index'])->name('flights.index');
        Route::get('/flights/create', [FlightPageController::class, 'create'])->name('flights.create');
        Route::get('/flights/{flight}/edit', [FlightPageController::class, 'edit'])->name('flights.edit');

        Route::get('/tourist-visas', [TouristVisaPageController::class, 'index'])->name('tourist-visas.index');
        Route::get('/tourist-visas/{touristVisa}', [TouristVisaPageController::class, 'show'])->name('tourist-visas.show');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php';
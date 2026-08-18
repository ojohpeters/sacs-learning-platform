<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\LessonContentController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StudentController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    $featuredCourses = Course::where('is_published', true)
        ->withCount('enrollments')
        ->latest()
        ->take(6)
        ->get();

    $allCourses = Course::where('is_published', true)
        ->withCount('enrollments')
        ->latest()
        ->get();

    return view('welcome', compact('featuredCourses', 'allCourses'));
});

// Public course catalog
Route::get('/courses', [CourseCatalogController::class, 'index'])->name('courses.catalog');
Route::get('/courses/{course:slug}', [CourseCatalogController::class, 'show'])->name('courses.show');

// Free preview lesson (no enrollment required)
Route::get('/courses/{course:slug}/preview/{lesson}', [CourseCatalogController::class, 'preview'])->name('courses.preview');

// Access-controlled streaming of uploaded lesson files (free previews are public;
// everything else requires an active enrollment — enforced inside the controller).
Route::get('/learn/{course:slug}/lesson/{lesson}/content', [LessonContentController::class, 'stream'])->name('lesson.content');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/pay', [CheckoutController::class, 'initiatePayment'])->name('checkout.pay')->middleware('auth');

// Payment callback (no auth — Paystack redirects the browser here)
Route::get('/payment/callback', [PaymentCallbackController::class, 'handle'])->name('payment.callback');

// Paystack webhook (no auth/CSRF — verified via signature; see bootstrap/app.php)
Route::post('/payment/webhook', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');

// Public certificate verification (the "Verify at" link printed on certificates)
Route::get('/verify/{code}', [CertificateVerificationController::class, 'show'])->name('certificate.verify');

// Student dashboard & learning (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-courses', [StudentController::class, 'courses'])->name('student.courses');
    Route::get('/dashboard', function () {
        return redirect()->route('student.courses');
    })->name('dashboard');

    // Course learning
    Route::get('/learn/{course:slug}', [LearningController::class, 'show'])->name('learning.course');
    Route::get('/learn/{course:slug}/live-sessions', [LearningController::class, 'sessions'])->name('learning.sessions');
    Route::get('/learn/{course:slug}/lesson/{lesson}', [LearningController::class, 'showLesson'])->name('learning.lesson');
    Route::get('/learn/{course:slug}/quiz/{quiz}', [App\Http\Controllers\QuizController::class, 'show'])->name('quiz.show');
    Route::post('/learn/{course:slug}/quiz/{quiz}/attempt/{attempt}/save', [App\Http\Controllers\QuizController::class, 'saveAnswer'])->name('quiz.save-answer');
    Route::post('/learn/{course:slug}/quiz/{quiz}/attempt/{attempt}/submit', [App\Http\Controllers\QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/learn/{course:slug}/quiz/{quiz}/attempt/{attempt}/result', [App\Http\Controllers\QuizController::class, 'result'])->name('quiz.result');
    Route::get('/learn/{course:slug}/quiz/{quiz}/retake', [App\Http\Controllers\QuizController::class, 'retake'])->name('quiz.retake');

    Route::post('/learn/{course:slug}/lesson/{lesson}/complete', [LearningController::class, 'toggleComplete'])->name('learning.toggle-complete');
    Route::post('/learn/{course:slug}/lesson/{lesson}/heartbeat', [LearningController::class, 'heartbeat'])->name('learning.heartbeat');

    // Receipt routes
    Route::get('/receipt/{payment}', [ReceiptController::class, 'show'])->name('receipt.show');
    Route::get('/receipt/{payment}/download', [ReceiptController::class, 'download'])->name('receipt.download');

    // Course completion certificate (only unlocks when all lessons are complete)
    Route::get('/certificate/{course:slug}', [CertificateController::class, 'show'])->name('certificate.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Courses CRUD (no show — the admin edits/manages courses, never a public detail view)
    Route::resource('courses', AdminCourseController::class)->except('show');
    Route::get('/courses/{course}/curriculum', [AdminCourseController::class, 'curriculum'])->name('courses.curriculum');

    // Sections
    Route::post('/courses/{course}/sections', [AdminLessonController::class, 'storeSection'])->name('sections.store');
    Route::put('/sections/{section}', [AdminLessonController::class, 'updateSection'])->name('sections.update');
    Route::delete('/sections/{section}', [AdminLessonController::class, 'destroySection'])->name('sections.destroy');

    // Lessons
    Route::post('/sections/{section}/lessons', [AdminLessonController::class, 'store'])->name('lessons.store');
    Route::put('/lessons/{lesson}', [AdminLessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');

    // Quizzes
    Route::get('/courses/{course}/quizzes', [App\Http\Controllers\Admin\QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/sections/{section}/quizzes/create', [App\Http\Controllers\Admin\QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/sections/{section}/quizzes', [App\Http\Controllers\Admin\QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quiz}/edit', [App\Http\Controllers\Admin\QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [App\Http\Controllers\Admin\QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [App\Http\Controllers\Admin\QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::post('/quizzes/{quiz}/questions', [App\Http\Controllers\Admin\QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
    Route::put('/questions/{question}', [App\Http\Controllers\Admin\QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
    Route::delete('/questions/{question}', [App\Http\Controllers\Admin\QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');

    // Sessions
    Route::get('/courses/{course}/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::post('/courses/{course}/sessions', [SessionController::class, 'store'])->name('sessions.store');
    Route::put('/sessions/{session}', [SessionController::class, 'update'])->name('sessions.update');
    Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');

    // Users
    Route::resource('users', UserController::class)->only(['index', 'destroy']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
});

// Profile routes (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiTeacherController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\ToyController;
use App\Http\Controllers\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\TestQuestionImportController as AdminTestQuestionImportController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Topics
    Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');

    // Topic quiz
    Route::get('/topics/{topic}/quiz', [TestController::class, 'topicQuizStart'])->name('test.topic.start');
    Route::post('/topics/{topic}/quiz', [TestController::class, 'topicTake'])->name('test.topic.take');

    // General test
    Route::get('/test', [TestController::class, 'start'])->name('test.start');
    Route::post('/test/take', [TestController::class, 'take'])->name('test.take');
    Route::post('/test/submit', [TestController::class, 'submit'])->name('test.submit');

    // Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');

    // Toys / Mini games
    Route::get('/toys', [ToyController::class, 'index'])->name('toys.index');
    Route::post('/toys/score', [ToyController::class, 'storeScore'])->name('toys.score');
    Route::get('/toys/speedrun', [ToyController::class, 'speedrun'])->name('toys.speedrun');
    Route::get('/toys/patterns', [ToyController::class, 'patterns'])->name('toys.patterns');
    Route::get('/toys/algebra', [ToyController::class, 'algebra'])->name('toys.algebra');
    Route::get('/toys/geometry', [ToyController::class, 'geometry'])->name('toys.geometry');

    // AI Teacher
    Route::get('/ai-teacher', [AiTeacherController::class, 'index'])->name('ai-teacher.index');
    Route::post('/ai-teacher/message', [AiTeacherController::class, 'send'])->name('ai-teacher.send');
    Route::get('/ai-teacher/conversations/{conversation}', [AiTeacherController::class, 'show'])->name('ai-teacher.show');

    /*
    |--------------------------------------------------------------------------
    | Admin routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Topics
        Route::get('/topics', [AdminTopicController::class, 'index'])->name('topics.index');
        Route::get('/topics/create', [AdminTopicController::class, 'create'])->name('topics.create');
        Route::post('/topics', [AdminTopicController::class, 'store'])->name('topics.store');
        Route::get('/topics/{topic}/edit', [AdminTopicController::class, 'edit'])->name('topics.edit');
        Route::put('/topics/{topic}', [AdminTopicController::class, 'update'])->name('topics.update');
        Route::delete('/topics/{topic}', [AdminTopicController::class, 'destroy'])->name('topics.destroy');

        // Questions
        Route::get('/questions', [AdminQuestionController::class, 'index'])->name('questions.index');
        Route::get('/questions/create', [AdminQuestionController::class, 'create'])->name('questions.create');
        Route::post('/questions', [AdminQuestionController::class, 'store'])->name('questions.store');
        Route::get('/questions/{question}/edit', [AdminQuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/questions/{question}', [AdminQuestionController::class, 'update'])->name('questions.update');
        Route::delete('/questions/{question}', [AdminQuestionController::class, 'destroy'])->name('questions.destroy');

        // Tests
        Route::get('/tests', [AdminTestController::class, 'index'])->name('tests.index');
        Route::get('/tests/import', [AdminTestQuestionImportController::class, 'showUpload'])->name('tests.import');
        Route::post('/tests/import', [AdminTestQuestionImportController::class, 'uploadPdf'])->name('tests.import.upload');
        Route::get('/tests/import/preview', [AdminTestQuestionImportController::class, 'preview'])->name('tests.import.preview');
        Route::post('/tests/import/preview', [AdminTestQuestionImportController::class, 'updatePreview'])->name('tests.import.update');
        Route::post('/tests/import/save', [AdminTestQuestionImportController::class, 'save'])->name('tests.import.save');
        Route::get('/tests/create', [AdminTestController::class, 'create'])->name('tests.create');
        Route::post('/tests', [AdminTestController::class, 'store'])->name('tests.store');
        Route::get('/tests/{test}/edit', [AdminTestController::class, 'edit'])->name('tests.edit');
        Route::put('/tests/{test}', [AdminTestController::class, 'update'])->name('tests.update');
        Route::delete('/tests/{test}', [AdminTestController::class, 'destroy'])->name('tests.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Guest routes (Login / Register)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors(['email' => 'Имэйл эсвэл нууц үг буруу байна'])
            ->onlyInput('email');
    })->name('login.post');

    // Register
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    })->name('register.post');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

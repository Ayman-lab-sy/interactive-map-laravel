<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\SiteSettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VController;

use Illuminate\Support\Str;
use TCG\Voyager\Events\Routing;
use TCG\Voyager\Events\RoutingAdmin;
use TCG\Voyager\Events\RoutingAdminAfter;
use TCG\Voyager\Events\RoutingAfter;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerAuthController;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\NewsController;
use App\Models\News;
use App\Services\Assistant\AssistantEngine;
use App\Http\Controllers\Admin\AssistantAdminController;
use App\Http\Controllers\Admin\AssistantKnowledgeAdminController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\CaseReportController;
use App\Http\Controllers\Admin\CaseController as AdminCaseController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\ReferralIndexController;
use App\Http\Controllers\Admin\ReferralReportController;
use App\Http\Controllers\Admin\ReferralPdfController;
use App\Http\Controllers\Admin\CaseStatusController;
use App\Http\Controllers\Admin\ReferralEditorialController;
use App\Http\Controllers\Admin\ReferralAnalyticalController;
use App\Http\Controllers\Admin\ReferralUnSpTortureController;
use App\Http\Controllers\Admin\ReferralUnSpEnforcedDisappearanceController;
use App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController;
use App\Http\Controllers\Admin\ReferralUnSpFreedomExpressionController;
use App\Http\Controllers\Admin\ReferralUnSpHumanRightsDefendersController;
use App\Http\Controllers\Admin\ReferralUnSpExtrajudicialExecutionsController;
use App\Http\Controllers\Admin\ReferralUnSpViolenceAgainstWomenController;
use App\Http\Controllers\Admin\ReferralUnSpMinorityIssuesController;
use App\Http\Controllers\Admin\ReferralUnSpFreedomReligionController;
use App\Http\Controllers\Admin\ReferralAmnestyEditorialController;
use App\Http\Controllers\Admin\ReferralHumanitarianICRCController;
use App\Http\Controllers\Admin\ReferralHumanitarianUNHCRController;
use App\Http\Controllers\Admin\ReferralUNOHCHRController;
use App\Http\Controllers\Admin\EntityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CaseFileController;
use App\Http\Controllers\Admin\ReferralNgoHrwController;
use App\Http\Controllers\Admin\ReferralAssistantController;
use App\Http\Controllers\Admin\ReferralExecutionController;
use App\Http\Controllers\EventController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/ar');
});


Route::get('/sitemap.xml', function () {

    $base = 'https://www.thealawites.com';

    $staticPages = [
        '/ar',
        '/en',
        '/ar/about',
        '/en/about',
        '/ar/news-new',
        '/en/news-new',
        '/ar/documentation-new',
        '/en/documentation-new',
        '/ar/donate',
        '/en/donate',
        '/ar/join',
        '/en/join',
        '/ar/contact',
        '/en/contact',
        '/ar/privacy-new',
        '/en/privacy-new',
    ];

    $news = \App\Models\News::where('published', 1)->get();

    return response()->view('sitemap', [
        'base' => $base,
        'staticPages' => $staticPages,
        'news' => $news
    ])->header('Content-Type', 'application/xml');
});


Route::prefix('{locale}')->where(['locale' => '[a-zA-Z]{2}'])->middleware(['setLocale', 'analytics'])->group(function () {

    Route::get('/og-image/{id}', [EventController::class, 'ogImage'])
        ->whereNumber('id');

    Route::post('/update-event/{id}', function($id) {
        dd('وصل الطلب', $id);
    });

    Route::get('/', function () {
        $latestNews = News::where('published', true)
            ->orderBy('date', 'desc')
            ->take(4)
            ->get();

        $view = app()->getLocale() === 'en'
            ? 'home2_en'
            : 'home2';

        return view($view, compact('latestNews'));

    })->name('home');
    
    // JOIN (عرض الصفحة)
    Route::get('/join', [Controller::class, 'join'])
        ->name('join');

    // JOIN (إرسال الطلب)
    Route::post('/join', [Controller::class, 'joinPost'])
        ->name('join.post');

    // VERIFY
    Route::get('/verify/{member_id}/{vCode?}', [Controller::class, 'verifyPage'])->whereNumber('member_id')->name('verify.index');
    Route::post('/verify/{member_id}/{vCode?}', [Controller::class, 'verifyPagePost'])->name('verify.post');

    Route::get('/page/{slug}', [Controller::class, 'sitePage'])->name('site.page');

    Route::get('/about', function () {
        return view(app()->getLocale() === 'en' ? 'about_en' : 'about');
    })->name('about');

    Route::get('/donate', function () {
        return view(app()->getLocale() === 'en'
            ? 'donate_new_en'
            : 'donate_new'
        );
    })->name('donate');

    Route::get('/contact', function () {
        return view(app()->getLocale() === 'en' ? 'contact_en' : 'contact_new');
    });

    Route::post('/contact/send', [ContactFormController::class, 'send'])
        ->middleware('throttle:5,1')
        ->name('contact.send');

    Route::get('/privacy-new', function () {
        return view(app()->getLocale() === 'en'
            ? 'privacy_new_en'
            : 'privacy_new'
        );
    })->name('privacy.new');
    
    Route::get('/documentation-new', function () { return view(app()->getLocale() === 'en' ? 'documentation_new_en' : 'documentation_new' ); })->name('documentation.new');

    //الروت الجديد الذي سوف يعتمد لاحقا
    Route::get('/news-new', [Controller::class, 'publicNews'])->name('news.new');
    Route::get('/news-new/{slug}', [Controller::class, 'publicNewsShow'])->name('news.show');

    Route::get('/thank-you', function () {
        return view(app()->getLocale() === 'en'
            ? 'thank_you_en'
            : 'thank_you'
        );
    })->name('thank.you');

    Route::post('/submit-report', [CaseController::class, 'store'])
        ->middleware(['throttle:case-store', 'log.rate.limit'])
        ->name('case.store');

    Route::get('/case/success', function () {
        if (!session()->pull('case_created')) {
            return redirect('/');
        }

        return view(
            app()->getLocale() === 'en'
                ? 'case.success_en'
                : 'case.success'
        );
    })->name('case.success');


    Route::get('/documentation/follow-up', [CaseController::class, 'followupForm'])
        ->name('case.followup.form');

    Route::post('/documentation/follow-up', [CaseController::class, 'followupStore'])
        ->name('case.followup.store');

    Route::get('/documentation/follow-up/success', function () {
        if (!session()->pull('case_updated')) {
            return redirect('/');
        }

        return view(
            app()->getLocale() === 'en'
                ? 'case.followup-success_en'
                : 'case.followup-success'
        );
    })->name('case.followup.success');

    //الخريطة
    Route::get('/map', function () {
        return view('map');
    })->name('map');
    Route::get('/add-event', function () {
        if (!auth()->check() || auth()->user()->role_id !== 1) {
            abort(403);
        }
        return view('add-event');
    })->name('add-event');

    

    Route::post('/add-event', [EventController::class, 'store'])->name('store-event');
    Route::post('/update-event/{id}', [EventController::class, 'update']);
    Route::get('/edit-event/{id}', [EventController::class, 'edit'])->name('edit-event');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('delete-event');
    Route::get('/event/{id}', function ($locale, $id) {
        $query = request()->getQueryString();
        if ($query) {
            return redirect("/{$locale}/map?event={$id}&{$query}");
        }
        return redirect("/{$locale}/map?event={$id}");    
    })->where('id', '[0-9]+');
    
    Route::get('/event-preview/{id}', function ($locale, $id) {
        $event = \DB::selectOne("
            SELECT * FROM h96737_news.events WHERE id = ?
        ", [$id]);
        if (!$event) {
            abort(404);
        }
        return view('event-preview', [
            'event' => $event,
            'locale' => $locale
        ]);
    });
    
    

    Route::get('/test-event/{id}', function ($locale, $id) {

        $event = \DB::selectOne("
            SELECT image FROM h96737_news.events WHERE id = ?
        ", [$id]);

        if (!$event || !$event->image) {
            return redirect('/images/og-default.jpg');
        }

        return redirect('/storage/' . $event->image);

    })->whereNumber('id');

    
});


Route::prefix('admin')->group(function () {
    Route::get('login', [VoyagerAuthController::class, 'login'])->name('voyager.login');
    Route::post('login', [VoyagerAuthController::class, 'postLogin'])->name('voyager.postlogin');
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin.user']], function () {


    // إعدادات عامة
    Route::post('/site-settings/update-all', [SiteSettingsController::class, 'updateAll'])
        ->name('siteSettings.updateAll');

    Route::post('/upload/media', [VController::class, 'upload'])
        ->name('panelMedia.upload');

    // الأخبار
    Route::resource('news', NewsController::class)
        ->names('admin.news');

    // 🧠 المساعد الذكي (Dashboard)
    Route::get('/assistant', [AssistantAdminController::class, 'index'])
        ->name('admin.assistant.index');

    Route::get('/assistant/approve/{id}', [AssistantAdminController::class, 'approve'])
        ->name('admin.assistant.approve');

    Route::post('/assistant/approve/{id}', [AssistantAdminController::class, 'store'])
        ->name('admin.assistant.store');

    Route::get('/assistant/convert/{id}', [AssistantAdminController::class, 'convert'])
        ->name('admin.assistant.convert');

    Route::post('/assistant/convert', [AssistantAdminController::class, 'storeConvert'])
        ->name('admin.assistant.convert.store');

    Route::post('/assistant/ignore', [AssistantAdminController::class, 'ignore'])
        ->name('admin.assistant.ignore');

    Route::get('/assistant/audit', [AssistantAdminController::class, 'audit'])
        ->name('admin.assistant.audit');

    // 📚 إدارة معرفة المساعد
    Route::get('/assistant/knowledge', [AssistantKnowledgeAdminController::class, 'index'])
        ->name('admin.assistant.knowledge');

    Route::get('/assistant/knowledge/{entry}/edit', [AssistantKnowledgeAdminController::class, 'edit'])
        ->name('admin.assistant.knowledge.edit');

    Route::post('/assistant/knowledge/{entry}/update', [AssistantKnowledgeAdminController::class, 'update'])
        ->name('admin.assistant.knowledge.update');
    //تجريب

    Route::get('/cases/{id}', [AdminCaseController::class, 'show'])
        ->name('admin.cases.show');
    
    Route::post('/cases/{id}/review', [AdminCaseController::class, 'submitReview'])
        ->name('admin.cases.review');

    /*Route::get('/admin/cases/{id}/export', [CaseReportController::class, 'generate'])
        ->name('admin.cases.export');*/

    Route::get('/cases', [AdminCaseController::class, 'index'])
        ->name('admin.cases.index');

    Route::post('/cases/{id}/reveal-field', [AdminCaseController::class, 'revealField'])
        ->name('admin.cases.revealField');
    
    
    //maps
    Route::get('/map-dashboard', function () {
        return view('admin.map-dashboard');
    })->name('admin.map.dashboard');
    
    //التقرير الجديد

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/reports', function () {
        return view('admin.reports.index'); // لاحقًا
    })->name('admin.reports.index');

    // =====================
    // Entities Management
    // =====================
    Route::prefix('entities')->name('admin.entities.')->group(function () {

        Route::get('/', [EntityController::class, 'index'])
            ->name('index');

        Route::get('{entity}/edit', [EntityController::class, 'edit'])
            ->name('edit');

        Route::put('{entity}', [EntityController::class, 'update'])
            ->name('update');

        Route::post('{entity}/toggle', [EntityController::class, 'toggleActive'])
            ->name('toggle');
    });


    Route::get('/case-files/{id}/view', [CaseFileController::class, 'view'])
        ->name('admin.files.view');

    Route::get('/case-files/{id}/download', [CaseFileController::class, 'download'])
        ->name('admin.files.download');

    Route::get('/cases/{caseId}/referrals/create', [ReferralController::class, 'create'])
        ->name('admin.referrals.create');

    Route::post('/cases/{caseId}/referrals', [ReferralController::class, 'store'])
        ->name('admin.referrals.store');
    
    Route::get('/referrals', [ReferralIndexController::class, 'index'])
        ->name('admin.referrals.index');

    Route::get('/referrals/{id}', [\App\Http\Controllers\Admin\ReferralDetailsController::class, 'show'])
       ->name('admin.referrals.show');

    Route::post(
        '/referrals/{id}/save-narrative',
        [\App\Http\Controllers\Admin\ReferralDetailsController::class, 'saveNarrative']
    )->name('admin.referrals.save-narrative');

    Route::get('/referrals/{id}/generate-report', [ReferralReportController::class, 'generate'])
       ->name('admin.referrals.generate-report');

    Route::get('/referrals/{id}/download-pdf', [ReferralPdfController::class, 'download'])
       ->name('admin.referrals.download-pdf');

    Route::post('/cases/{case}/under-review', [CaseStatusController::class,'toUnderReview'])
        ->name('cases.under-review');

    Route::post('/cases/{case}/documented', [CaseStatusController::class,'toDocumented'])
        ->name('cases.documented');

    Route::post('/cases/{case}/archive', [CaseStatusController::class,'archive'])
        ->name('cases.archive');

    Route::post('/cases/{case}/add-note', [CaseStatusController::class, 'addNote'])
        ->name('cases.add-note');

    Route::post(
        '/referrals/{id}/mark-ready',
        [\App\Http\Controllers\Admin\ReferralStatusController::class, 'markReady']
    )->name('admin.referrals.mark-ready');
    
    Route::get('/reports/generated', [\App\Http\Controllers\Admin\GeneratedReportsController::class, 'index'])
        ->name('admin.reports.generated');

    Route::post(
        '/referrals/{id}/save-summary-controls',
        [ReferralEditorialController::class, 'saveSummaryControls']
    )->name('admin.referrals.save-summary-controls');

    Route::post(
        '/referrals/{id}/save-analytical',
        [ReferralEditorialController::class, 'saveAnalyticalContent']
    )->name('admin.referrals.save-analytical');

    Route::get(
        '/referrals/{id}/un-sp/torture',
        [\App\Http\Controllers\Admin\ReferralUnSpTortureController::class, 'show']
    )->name('admin.referrals.unsp.torture.show');

    Route::post(
        '/referrals/{id}/un-sp/torture/summary',
        [ReferralUnSpTortureController::class, 'saveSummary']
    )->name('admin.referrals.unsp.torture.save-summary');

    Route::post(
        '/referrals/{id}/un-sp/torture/victim',
        [ReferralUnSpTortureController::class, 'saveVictim']
    )->name('admin.referrals.unsp.torture.save-victim');

    Route::post(
        '/referrals/{id}/un-sp/torture/perpetrators',
        [ReferralUnSpTortureController::class, 'savePerpetrators']
    )->name('admin.referrals.unsp.torture.save-perpetrators');

    Route::post(
        '/referrals/{id}/un-sp/torture/context',
        [ReferralUnSpTortureController::class, 'saveContext']
    )->name('admin.referrals.unsp.torture.save-context');

    Route::post(
        '/referrals/{id}/un-sp/torture/skip-context',
        [ReferralUnSpTortureController::class, 'skipContext']
    )->name('admin.referrals.unsp.torture.skip-context');

    Route::post(
        '/referrals/{id}/un-sp/torture/remedies',
        [ReferralUnSpTortureController::class, 'saveRemedies']
    )->name('admin.referrals.unsp.torture.save-remedies');

    Route::post(
        '/referrals/{id}/un-sp/generate-report',
        [ReferralReportController::class, 'generate']
    )->name('admin.referrals.unsp.generate-report');

    //cover letter
    Route::get(
        '/referrals/{id}/un/cover-letter',
        [\App\Http\Controllers\Admin\UnCoverLetterController::class, 'generate']
    )->name('admin.referrals.un.cover-letter');

    Route::get(
        '/referrals/{id}/un/cover-letter/download',
        [\App\Http\Controllers\Admin\UnCoverLetterPdfController::class, 'download']
    )->name('admin.referrals.un.cover-letter.download');

    //arbitrary-detention
    Route::get(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'show']
    )->name('admin.referrals.un_sp.arbitrary_detention.show');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/summary',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'saveSummary']
    )->name('admin.referrals.un_sp.arbitrary_detention.save_summary');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/victim',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'saveVictim']
    )->name('admin.referrals.un_sp.arbitrary_detention.save_victim');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/detention',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'saveDetentionDetails']
    )->name('admin.referrals.un_sp.arbitrary_detention.save_detention');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/legal-basis',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'saveLegalBasis']
    )->name('admin.referrals.un_sp.arbitrary_detention.save_legal_basis');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/procedural-violations',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'saveProceduralViolations']
    )->name('admin.referrals.un_sp.arbitrary_detention.save_procedural_violations');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/context',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'saveContext']
    )->name('admin.referrals.un_sp.arbitrary_detention.save_context');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/context/skip',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'skipContext']
    )->name('admin.referrals.un_sp.arbitrary_detention.skip_context');

    Route::post(
        '/admin/referrals/un-sp/arbitrary-detention/{referral}/remedies',
        [\App\Http\Controllers\Admin\ReferralUnSpArbitraryDetentionController::class, 'saveRemedies']
    )->name('admin.referrals.un_sp.arbitrary_detention.save_remedies');


    /*
    |--------------------------------------------------------------------------
    | UN Special Procedures
    |--------------------------------------------------------------------------
    */
    Route::prefix('referrals/un-sp')->group(function () {

        // ✅ Enforced Disappearance
        Route::prefix('enforced-disappearance')->name('admin.referrals.un_sp.enforced_disappearance.')->group(function () {
            Route::get('{referral}', [ReferralUnSpEnforcedDisappearanceController::class, 'show'])->name('show');
            Route::post('{referral}/summary', [ReferralUnSpEnforcedDisappearanceController::class, 'saveSummary'])->name('save_summary');
            Route::post('{referral}/victim', [ReferralUnSpEnforcedDisappearanceController::class, 'saveVictim'])->name('save_victim');
            Route::post('{referral}/circumstances', [ReferralUnSpEnforcedDisappearanceController::class, 'saveCircumstances'])->name('save_circumstances');
            Route::post('{referral}/perpetrators', [ReferralUnSpEnforcedDisappearanceController::class, 'savePerpetrators'])->name('save_perpetrators');
            Route::post('{referral}/context', [ReferralUnSpEnforcedDisappearanceController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/remedies', [ReferralUnSpEnforcedDisappearanceController::class, 'saveRemedies'])->name('save_remedies');
            Route::post('{referral}/context-skip', [ReferralUnSpEnforcedDisappearanceController::class, 'skipContext'])->name('skip_context');
        });

        // ✅ Freedom of Expression
        Route::prefix('freedom-expression')->name('admin.referrals.un_sp.freedom_expression.')->group(function () {
            Route::get('{referral}', [ReferralUnSpFreedomExpressionController::class, 'show'])->name('show');
            Route::post('{referral}/summary', [ReferralUnSpFreedomExpressionController::class, 'saveSummary'])->name('save_summary');
            Route::post('{referral}/victim', [ReferralUnSpFreedomExpressionController::class, 'saveVictim'])->name('save_victim');
            Route::post('{referral}/expression', [ReferralUnSpFreedomExpressionController::class, 'saveExpressionActivity'])->name('save_expression');
            Route::post('{referral}/violations', [ReferralUnSpFreedomExpressionController::class, 'saveViolations'])->name('save_violations');
            Route::post('{referral}/legal-basis', [ReferralUnSpFreedomExpressionController::class, 'saveLegalBasis'])->name('save_legal_basis');
            Route::post('{referral}/context', [ReferralUnSpFreedomExpressionController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/context/skip', [ReferralUnSpFreedomExpressionController::class, 'skipContext'])->name('skip_context');
            Route::post('{referral}/remedies', [ReferralUnSpFreedomExpressionController::class, 'saveRemedies'])->name('save_remedies');
        });

        // ✅ Human Rights Defenders
        Route::prefix('human-rights-defenders')->name('admin.referrals.un_sp.human_rights_defenders.')->group(function () {
            Route::get('{referral}', [ReferralUnSpHumanRightsDefendersController::class, 'show'])->name('show');
            Route::post('{referral}/summary', [ReferralUnSpHumanRightsDefendersController::class, 'saveSummary'])->name('save_summary');
            Route::post('{referral}/victim', [ReferralUnSpHumanRightsDefendersController::class, 'saveVictim'])->name('save_victim');
            Route::post('{referral}/role', [ReferralUnSpHumanRightsDefendersController::class, 'saveDefenderRole'])->name('save_role');
            Route::post('{referral}/activities', [ReferralUnSpHumanRightsDefendersController::class, 'saveActivities'])->name('save_activities');
            Route::post('{referral}/targeting', [ReferralUnSpHumanRightsDefendersController::class, 'saveTargetingLink'])->name('save_targeting');
            Route::post('{referral}/violations', [ReferralUnSpHumanRightsDefendersController::class, 'saveViolations'])->name('save_violations');
            Route::post('{referral}/legal-basis', [ReferralUnSpHumanRightsDefendersController::class, 'saveLegalBasis'])->name('save_legal_basis');
            Route::post('{referral}/context', [ReferralUnSpHumanRightsDefendersController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/context/skip', [ReferralUnSpHumanRightsDefendersController::class, 'skipContext'])->name('skip_context');
            Route::post('{referral}/remedies', [ReferralUnSpHumanRightsDefendersController::class, 'saveRemedies'])->name('save_remedies');
        });

        // ✅ Extrajudicial / Summary / Arbitrary Executions
        Route::prefix('extrajudicial-executions')->name('admin.referrals.un_sp.extrajudicial_executions.')->group(function () {
            Route::get('{referral}', [ReferralUnSpExtrajudicialExecutionsController::class, 'show'])->name('show');
            Route::post('{referral}/summary', [ReferralUnSpExtrajudicialExecutionsController::class, 'saveSummary'])->name('save_summary');
            Route::post('{referral}/victim', [ReferralUnSpExtrajudicialExecutionsController::class, 'saveVictim'])->name('save_victim');
            Route::post('{referral}/circumstances', [ReferralUnSpExtrajudicialExecutionsController::class, 'saveCircumstances'])->name('save_circumstances');
            Route::post('{referral}/perpetrators', [ReferralUnSpExtrajudicialExecutionsController::class, 'savePerpetrators'])->name('save_perpetrators');
            Route::post('{referral}/context', [ReferralUnSpExtrajudicialExecutionsController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/context/skip', [ReferralUnSpExtrajudicialExecutionsController::class, 'skipContext'])->name('skip_context');
            Route::post('{referral}/remedies', [ReferralUnSpExtrajudicialExecutionsController::class, 'saveRemedies'])->name('save_remedies');
        });

        // ✅ Violence against Women
        Route::prefix('violence-against-women')->name('admin.referrals.un_sp.violence_against_women.')->group(function () {
            Route::get('{referral}', [ReferralUnSpViolenceAgainstWomenController::class, 'show'])->name('show');
            Route::post('{referral}/summary', [ReferralUnSpViolenceAgainstWomenController::class, 'saveSummary'])->name('save_summary');
            Route::post('{referral}/victim', [ReferralUnSpViolenceAgainstWomenController::class, 'saveVictim'])->name('save_victim');
            Route::post('{referral}/violence', [ReferralUnSpViolenceAgainstWomenController::class, 'saveViolence'])->name('save_violence');
            Route::post('{referral}/perpetrators', [ReferralUnSpViolenceAgainstWomenController::class, 'savePerpetrators'])->name('save_perpetrators');
            Route::post('{referral}/context', [ReferralUnSpViolenceAgainstWomenController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/context/skip', [ReferralUnSpViolenceAgainstWomenController::class, 'skipContext'])->name('skip_context');
            Route::post('{referral}/remedies', [ReferralUnSpViolenceAgainstWomenController::class, 'saveRemedies'])->name('save_remedies');
        });

        // ✅ Minority Issues
        Route::prefix('minority-issues')->name('admin.referrals.un_sp.minority_issues.')->group(function () {
            Route::get('{referral}', [ReferralUnSpMinorityIssuesController::class, 'show'])->name('show');

            Route::post('{referral}/summary', [ReferralUnSpMinorityIssuesController::class, 'saveSummary'])->name('save_summary');
            Route::post('{referral}/victim', [ReferralUnSpMinorityIssuesController::class, 'saveVictim'])->name('save_victim');
            Route::post('{referral}/identity', [ReferralUnSpMinorityIssuesController::class, 'saveIdentity'])->name('save_identity');
            Route::post('{referral}/violations', [ReferralUnSpMinorityIssuesController::class, 'saveViolation'])->name('save_violations');
            Route::post('{referral}/perpetrators', [ReferralUnSpMinorityIssuesController::class, 'savePerpetrators'])->name('save_perpetrators');
            Route::post('{referral}/context', [ReferralUnSpMinorityIssuesController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/context/skip', [ReferralUnSpMinorityIssuesController::class, 'skipContext'])->name('skip_context');
            Route::post('{referral}/remedies', [ReferralUnSpMinorityIssuesController::class, 'saveRemedies'])->name('save_remedies');
        });

        // ✅ Freedom of Religion or Belief
        Route::prefix('freedom-religion')->name('admin.referrals.un_sp.freedom_religion.')->group(function () {
            Route::get('{referral}', [ReferralUnSpFreedomReligionController::class, 'show'])->name('show');

            Route::post('{referral}/summary', [ReferralUnSpFreedomReligionController::class, 'saveSummary'])->name('save_summary');
            Route::post('{referral}/victim', [ReferralUnSpFreedomReligionController::class, 'saveVictim'])->name('save_victim');
            Route::post('{referral}/identity', [ReferralUnSpFreedomReligionController::class, 'saveIdentity'])->name('save_identity');
            Route::post('{referral}/violations', [ReferralUnSpFreedomReligionController::class, 'saveViolation'])->name('save_violations');
            Route::post('{referral}/perpetrators', [ReferralUnSpFreedomReligionController::class, 'savePerpetrators'])->name('save_perpetrators');
            Route::post('{referral}/context', [ReferralUnSpFreedomReligionController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/context/skip', [ReferralUnSpFreedomReligionController::class, 'skipContext'])->name('skip_context');
            Route::post('{referral}/remedies', [ReferralUnSpFreedomReligionController::class, 'saveRemedies'])->name('save_remedies');
        });

    });

    /*
    |--------------------------------------------------------------------------
    | Amnesty International – NGO Legal Editorial
    |--------------------------------------------------------------------------
    */
    Route::get('admin/referrals/amnesty/{referral}',[ReferralAmnestyEditorialController::class, 'show'])->name('admin.referrals.amnesty.show');
    Route::post('/referrals/{id}/amnesty/source-account',[ReferralAmnestyEditorialController::class, 'saveSourceAccount'])->name('admin.referrals.amnesty.save-source');
    Route::post('/referrals/{id}/amnesty/case-summary',[ReferralAmnestyEditorialController::class, 'saveCaseSummary'])->name('admin.referrals.amnesty.save-summary');
    Route::post('/referrals/{id}/amnesty/optional',[ReferralAmnestyEditorialController::class, 'saveOptional'])->name('admin.referrals.amnesty.save-optional');

    /*
    |--------------------------------------------------------------------------
    | Humanitarian Protection – ICRC
    |--------------------------------------------------------------------------
    */
    Route::prefix('referrals/humanitarian')->group(function () {
        Route::prefix('icrc')->name('admin.referrals.humanitarian.icrc.')->group(function () {

            Route::get('{referral}', [ReferralHumanitarianICRCController::class, 'show'])->name('show');
            Route::post('{referral}/source', [ReferralHumanitarianICRCController::class, 'saveSource'])->name('save_source');
            Route::post('{referral}/location-time', [ReferralHumanitarianICRCController::class, 'saveLocationTime'])->name('save_location_time');
            Route::post('{referral}/needs', [ReferralHumanitarianICRCController::class, 'saveNeeds'])->name('save_needs');
            Route::post('{referral}/risks', [ReferralHumanitarianICRCController::class, 'saveRisks'])->name('save_risks');
            Route::post('{referral}/mandate', [ReferralHumanitarianICRCController::class, 'saveMandate'])->name('save_mandate');
            Route::post('{referral}/snapshot', [ReferralHumanitarianICRCController::class, 'saveSnapshot'])->name('save_snapshot');
            Route::post('{referral}/assistance', [ReferralHumanitarianICRCController::class, 'saveAssistance'])->name('save_assistance');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Humanitarian Protection – UNHCR
    |--------------------------------------------------------------------------
    */
    Route::prefix('referrals/humanitarian')->group(function () {
        Route::prefix('unhcr')->name('admin.referrals.humanitarian.unhcr.')->group(function () {

            Route::get('{referral}', [ReferralHumanitarianUNHCRController::class, 'show'])->name('show');
            Route::post('{referral}/source', [ReferralHumanitarianUNHCRController::class, 'saveSource'])->name('save_source');
            Route::post('{referral}/location-time', [ReferralHumanitarianUNHCRController::class, 'saveLocationTime'])->name('save_location_time');
            Route::post('{referral}/needs', [ReferralHumanitarianUNHCRController::class, 'saveNeeds'])->name('save_needs');
            Route::post('{referral}/risks', [ReferralHumanitarianUNHCRController::class, 'saveRisks'])->name('save_risks');
            Route::post('{referral}/mandate', [ReferralHumanitarianUNHCRController::class, 'saveMandate'])->name('save_mandate');
            Route::post('{referral}/snapshot', [ReferralHumanitarianUNHCRController::class, 'saveSnapshot'])->name('save_snapshot');
            Route::post('{referral}/assistance', [ReferralHumanitarianUNHCRController::class, 'saveAssistance'])->name('save_assistance');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | UN Accountability – OHCHR
    |--------------------------------------------------------------------------
    */
    Route::prefix('referrals/un-accountability')->group(function () {
        Route::prefix('ohchr')->name('admin.referrals.un_accountability.ohchr.')->group(function () {

            Route::get('{referral}', [ReferralUNOHCHRController::class, 'show'])->name('show');
            Route::post('{referral}/context', [ReferralUNOHCHRController::class, 'saveContext'])->name('save_context');
            Route::post('{referral}/methodology', [ReferralUNOHCHRController::class, 'saveMethodology'])->name('save_methodology');
            Route::post('{referral}/location-time', [ReferralUNOHCHRController::class, 'saveLocationTime'])->name('save_location_time');
            Route::post('{referral}/documented-info', [ReferralUNOHCHRController::class, 'saveDocumentedInfo'])->name('save_documented_info');
            Route::post('{referral}/concerns', [ReferralUNOHCHRController::class, 'saveConcerns'])->name('save_concerns');
            Route::post('{referral}/pattern', [ReferralUNOHCHRController::class, 'savePattern'])->name('save_pattern');
            Route::post('{referral}/mandate', [ReferralUNOHCHRController::class, 'saveMandate'])->name('save_mandate');
            Route::post('{referral}/internal-notes', [ReferralUNOHCHRController::class, 'saveInternalNotes'])->name('save_internal_notes');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Human Rights Watch – NGO 
    |--------------------------------------------------------------------------
    */
    Route::prefix('referrals/ngo')->group(function () {
        Route::prefix('hrw')->name('admin.referrals.ngo.hrw.')->group(function () {
            // عرض الإحالة
            Route::get('{referral}', [ReferralNgoHrwController::class, 'show'])->name('show');
            // Block A – Legal Narrative
            Route::post('{referral}/narrative', [ReferralNgoHrwController::class, 'saveNarrative'])->name('save_narrative');
            // Block B – Summary Alignment
            Route::post('{referral}/summary-alignment', [ReferralNgoHrwController::class, 'saveSummaryAlignment'])->name('save_summary_alignment');
            // Block C – Analytical Content
            Route::post('{referral}/analytical', [ReferralNgoHrwController::class, 'saveAnalytical'])->name('save_analytical');
        });
    });


    Route::get(
        'assistant/cases/{case}',
        [ReferralAssistantController::class, 'show']
    )->name('admin.case_assistant.show');

    Route::post(
        'assistant/cases/{case}/analyze',
        [ReferralAssistantController::class, 'analyze']
    )->name('admin.case_assistant.analyze');

    
    Route::post(
        'assistant/cases/{case}/execute',
        [ReferralExecutionController::class, 'execute']
    )->name('admin.case_assistant.execute');





  
    // Voyager::routes();
    Route::group(['as' => 'voyager.'], function () {
        event(new Routing());

        $namespacePrefix = '\\'.config('voyager.controllers.namespace').'\\';

        Route::group(['middleware' => 'admin.user'], function () use ($namespacePrefix) {
            event(new RoutingAdmin());

            // Main Admin and Logout Route
            // Route::get('/', ['uses' => $namespacePrefix.'VoyagerController@index',   'as' => 'dashboard']);
            Route::get('/', ['uses' => 'App\Http\Controllers\DashController@index',   'as' => 'dashboard']);
            Route::post('logout', ['uses' => $namespacePrefix.'VoyagerController@logout',  'as' => 'logout']);
            Route::post('upload', ['uses' => $namespacePrefix.'VoyagerController@upload',  'as' => 'upload']);

            Route::get('profile', ['uses' => $namespacePrefix.'VoyagerUserController@profile', 'as' => 'profile']);

            try {
                foreach (Voyager::model('DataType')::all() as $dataType) {
                    $breadController = $dataType->controller
                                        ? Str::start($dataType->controller, '\\')
                                        : $namespacePrefix.'VoyagerBaseController';

                    Route::get($dataType->slug.'/order', $breadController.'@order')->name($dataType->slug.'.order');
                    Route::post($dataType->slug.'/action', $breadController.'@action')->name($dataType->slug.'.action');
                    Route::post($dataType->slug.'/order', $breadController.'@update_order')->name($dataType->slug.'.update_order');
                    Route::get($dataType->slug.'/{id}/restore', $breadController.'@restore')->name($dataType->slug.'.restore');
                    Route::get($dataType->slug.'/relation', $breadController.'@relation')->name($dataType->slug.'.relation');
                    Route::post($dataType->slug.'/remove', $breadController.'@remove_media')->name($dataType->slug.'.media.remove');
                    Route::resource($dataType->slug, $breadController, ['parameters' => [$dataType->slug => 'id']]);
                }
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Custom routes hasn't been configured because: ".$e->getMessage(), 1);
            } catch (\Exception $e) {
                // do nothing, might just be because table not yet migrated.
            }

            // Menu Routes
            Route::group([
                'as'     => 'menus.',
                'prefix' => 'menus/{menu}',
            ], function () use ($namespacePrefix) {
                Route::get('builder', ['uses' => $namespacePrefix.'VoyagerMenuController@builder',    'as' => 'builder']);
                Route::post('order', ['uses' => $namespacePrefix.'VoyagerMenuController@order_item', 'as' => 'order_item']);

                Route::group([
                    'as'     => 'item.',
                    'prefix' => 'item',
                ], function () use ($namespacePrefix) {
                    Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerMenuController@delete_menu', 'as' => 'destroy']);
                    Route::post('/', ['uses' => $namespacePrefix.'VoyagerMenuController@add_item',    'as' => 'add']);
                    Route::put('/', ['uses' => $namespacePrefix.'VoyagerMenuController@update_item', 'as' => 'update']);
                });
            });

            // Settings
            Route::group([
                'as'     => 'settings.',
                'prefix' => 'settings',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@index',        'as' => 'index']);
                Route::post('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@store',        'as' => 'store']);
                Route::put('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@update',       'as' => 'update']);
                Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerSettingsController@delete',       'as' => 'delete']);
                Route::get('{id}/move_up', ['uses' => $namespacePrefix.'VoyagerSettingsController@move_up',      'as' => 'move_up']);
                Route::get('{id}/move_down', ['uses' => $namespacePrefix.'VoyagerSettingsController@move_down',    'as' => 'move_down']);
                Route::put('{id}/delete_value', ['uses' => $namespacePrefix.'VoyagerSettingsController@delete_value', 'as' => 'delete_value']);
            });

            // Admin Media
            Route::group([
                'as'     => 'media.',
                'prefix' => 'media',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerMediaController@index',              'as' => 'index']);
                Route::post('files', ['uses' => $namespacePrefix.'VoyagerMediaController@files',              'as' => 'files']);
                Route::post('new_folder', ['uses' => $namespacePrefix.'VoyagerMediaController@new_folder',         'as' => 'new_folder']);
                Route::post('delete_file_folder', ['uses' => $namespacePrefix.'VoyagerMediaController@delete', 'as' => 'delete']);
                Route::post('move_file', ['uses' => $namespacePrefix.'VoyagerMediaController@move',          'as' => 'move']);
                Route::post('rename_file', ['uses' => $namespacePrefix.'VoyagerMediaController@rename',        'as' => 'rename']);
                Route::post('upload', ['uses' => $namespacePrefix.'VoyagerMediaController@upload',             'as' => 'upload']);
                Route::post('crop', ['uses' => $namespacePrefix.'VoyagerMediaController@crop',             'as' => 'crop']);
            });

            // BREAD Routes
            Route::group([
                'as'     => 'bread.',
                'prefix' => 'bread',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerBreadController@index',              'as' => 'index']);
                Route::get('{table}/create', ['uses' => $namespacePrefix.'VoyagerBreadController@create',     'as' => 'create']);
                Route::post('/', ['uses' => $namespacePrefix.'VoyagerBreadController@store',   'as' => 'store']);
                Route::get('{table}/edit', ['uses' => $namespacePrefix.'VoyagerBreadController@edit', 'as' => 'edit']);
                Route::put('{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@update',  'as' => 'update']);
                Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@destroy',  'as' => 'delete']);
                Route::post('relationship', ['uses' => $namespacePrefix.'VoyagerBreadController@addRelationship',  'as' => 'relationship']);
                Route::get('delete_relationship/{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@deleteRelationship',  'as' => 'delete_relationship']);
            });

            // Database Routes
            Route::resource('database', $namespacePrefix.'VoyagerDatabaseController');

            // Compass Routes
            Route::group([
                'as'     => 'compass.',
                'prefix' => 'compass',
            ], function () use ($namespacePrefix) {
                Route::get('/', ['uses' => $namespacePrefix.'VoyagerCompassController@index',  'as' => 'index']);
                Route::post('/', ['uses' => $namespacePrefix.'VoyagerCompassController@index',  'as' => 'post']);
            });

            event(new RoutingAdminAfter());
        });

        //Asset Routes
        Route::get('voyager-assets', ['uses' => $namespacePrefix.'VoyagerController@assets', 'as' => 'voyager_assets']);

        event(new RoutingAfter());
    });
});

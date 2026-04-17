<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\ContactInfo;
use App\Models\Member;
use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Notifications\VerifyNotification;
use App\Models\News;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Validator;
use View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index(Request $requset)
    {
        $sets = SiteSetting::allJson();
        $posts = BlogPost::orderBy('updated_at', 'desc')->limit(6)->get();
        $contact_header = ContactInfo::where('in_home', 1)->get()->groupBy('type');

        return view('home', [
            'sets' => $sets,
            'posts' => $posts,
            'cHeader' => $contact_header,
        ]);
    }

    public function donate(Request $request)
    {
        $set = SiteSetting::allJson(app()->getLocale(), 'donate');

        return view('donate', [
            'set' => $set
        ]);
    }

    public function join(Request $request)
    {
        $set = SiteSetting::allJson(app()->getLocale(), 'join');

        return view(
            app()->getLocale() === 'en' ? 'join_new_en' : 'join_new',
            [
                'verified' => false,
                'set' => $set
            ]
        );
    }


    public function joinPost(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'gender' => ['nullable', 'in:male,female,none'],
            'street' => ['required', 'string'],
            'postcode' => ['required', 'string', 'max:32'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'email' => ['required', 'string', 'max:255', 'unique:members,email'],
            'aggrement_1' => ['accepted'],
            'aggreement_2' => ['accepted'],
        ]);


        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid->errors())->withInput();
        } else {
            $member = Member::create($request->all());


            $member->notify(new VerifyNotification($member->id));

            return redirect(route('verify.index', $member->id));
        }
    }

    public function verifyPage(Request $request, $locale, $member_id, $vCode = null)
    {
        $member = Member::find($member_id);

        if (!$member) {
            return redirect()->route('home', ['locale' => $locale]);
        }

        // ✅ Auto verify from email link
        if ($vCode && $member->validation_code === $vCode) {
            $member->validation_code = null;
            $member->is_verified = true;
            $member->save();

            return view('join_success');
        }

        // Already verified
        if ($member->is_verified) {
            return view('join_success');
        }

        // Manual code entry
        return view('verify', [
            'user_id' => $member_id
        ]);
    }

    public function verifyPagePost(Request $request, $locale, $member_id, $vCode = null)
    {
        $member = Member::find($member_id);

        $valid = Validator::make($request->all(), [
            'validation_code' => ['required', 'numeric', 'digits:6', 'in:' . $member->validation_code]
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid->errors())->withInput();
        }

        $member->validation_code = null;
        $member->is_verified = true;
        $member->save();

        return view('join_success');
    }

    //المسار الجديد للاخبار والذي سيتم اعتماده لاحقا وحذف الباقي
    public function publicNews(Request $request)
    {
        $locale = app()->getLocale();

        $news = News::where('published', 1)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('news_public', compact('news', 'locale'));
    }

    public function publicNewsShow($locale, $slug)
    {
        $news = News::where('slug', $slug)
            ->where('published', 1)
            ->firstOrFail();

        return view('news_show', compact('news'));
    }

    
    public function news(Request $request)
    {
        $set = SiteSetting::allJson(app()->getLocale(), 'news');
        $news = BlogPost::orderBy('updated_at', 'desc')->paginate(10);


        return view('news', [
            'set' => $set,
            'news' => $news,
        ]);
    }

    public function newsPost(Request $request, $locale, $id)
    {
        $post = BlogPost::find($id);
        $post = $post->translate($locale, 'en');
        return view('newsPost', ['post' => $post]);
    }

    public function sitePage(Request $request, $locale, $slug)
    {
        $redirectMap = [
            'about-us'  => 'about',
            'our-goals' => 'home',
        ];

        if (array_key_exists($slug, $redirectMap)) {
            return redirect()->route($redirectMap[$slug], ['locale' => $locale], 301);
        }

        $page = SitePage::where('slug', $slug)->firstOrFail();
        $page = $page->translate($locale, 'en');

        return view('page', ['page' => $page]);
    }

}

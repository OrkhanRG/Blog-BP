<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Category;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class FrontController extends Controller
{
    //      Yalnız aid olduğu Controllerdəki View-larda (method-larda) Dəyişənləri paylaşır!

    /*
       public function __construct()
    {
        $settings = Settings::first();
        $categories = Category::query()->where('status', 1)->get();
        View::share(['settings' => $settings, 'categories' => $categories]);
    }
    */

    public function home()
    {
        return view('front.index');
    }

    public function category(Request $request, string $slug)
    {
        $category = Category::query()->with('articlesActive')->where('slug', $slug)->first();

//        $articles = $category->articlesActive()->with(['category', 'user'])->paginate(1);
//        $articles = $category->articlesActive()->paginate(1);
//        $articles->load(['user', 'category']);

        $articles = Article::query()
            ->with(['user:id,name,username', 'category:id,name'])
            ->whereHas('category', function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->whereNotNull('publish_date')
                    ->where('publish_date', '<=', now());;
            })->paginate(3);


        return view('front.article-list', compact('category', 'articles'));
    }

    public function articleDetail(Request $request, string $username, string $articleSlug)
    {

        $article = Article::query()->with([
            'user',
            'user.articleLike',
            'user.commentLike',
            'comments' => function ($query) {
            $query->where('status', 1)
                ->whereNull('parent_id');
            },
            'comments.commentLikes',
            'comments.user',
            'comments.children' => function ($query) {
                $query->where('status', 1);
            },
            'comments.children.user',
            'comments.children.commentLikes',
        ])
            ->where('slug', $articleSlug)
            ->first();
        $userLike = $article
            ->articleLikes
            ->where('article_id', $article->id)
            ->where('user_id', \auth()->id())
            ->first();

        $article->increment('view_count');
        $article->save();

        return view('front.article-detail', compact('article', 'userLike'));
    }

    public function articleComment(Request $request, Article $article)
    {
        $data = $request->except('_token');
        if (Auth::check())
        {
            $data['user_id'] = Auth::id();
        }
        $data['article_id'] = $article->id;
        $data['ip'] = $request->ip();

        ArticleComment::create($data);

        alert()->success("Uğurlu", "Mesajınız göndərildi. Yoxlamdan sonra mesajınız yayınlanacaq. Zəhmət olmasa təsdiq olmasını gözləyin")->showConfirmButton('yaxşı', '#3085d6')->autoClose(5000);
        return redirect()->back();
    }
}

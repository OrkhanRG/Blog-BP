<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Category;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        Log::warning('TEST LOG');
        Log::error('TEST LOG');
        Log::info('TEST LOG');
        Log::emergency('TEST LOG');
        Log::critical('TEST LOG');
        Log::alert('TEST LOG');
        Log::debug('TEST LOG');
        Log::notice('TEST LOG');


        $mostPopularArticles = Article::query()
            ->with(['user', 'category'])
            ->whereHas('user')
            ->whereHas('category')
            ->orderBy('view_count', 'DESC')
            ->limit(6)
            ->get();

        $lastPublishedArticles = Article::query()
            ->with('user', 'category')
            ->whereHas('user')
            ->whereHas('category')
            ->orderBy('publish_date', 'DESC')
            ->limit(6)
            ->get();

        return view('front.index', compact('mostPopularArticles', 'lastPublishedArticles'));
    }

    public function category(Request $request, string $slug)
    {
//        $articles = $category->articlesActive()->with(['category', 'user'])->paginate(1);
//        $articles = $category->articlesActive()->paginate(1);
//        $articles->load(['user', 'category']);

        $articles = Article::query()
            ->with(['user:id,name,username', 'category:id,name,slug'])
            ->whereHas('category', function ($query) use ($slug) {
                $query->where('slug', $slug);
                    /*->whereNotNull('publish_date')
                    ->where('publish_date', '<=', now());*/
            })
            ->paginate(21);

        $title = Category::query()->where('slug', $slug)->first()->name . ' Kateqoriyasına Aid Məqalələr';

        return view('front.article-list', compact( 'articles', 'title'));
    }

    public function articleDetail(Request $request, string $username, string $articleSlug)
    {
        $article = session()->get('last_article');
        $visitedArticles = session()->get('visited_articles');
        $visitedArticlesCategoryIds = [];
        $visitedArticlesAuthorIds = [];
        $visitedInfo = Article::query()
            ->select('category_id', 'user_id')
            ->whereIn('id', $visitedArticles)
            ->get();

        foreach ($visitedInfo as $item)
        {
            $visitedArticlesCategoryIds[] = $item->categroy_id;
            $visitedArticlesAuthorIds[] = $item->user_id;
        }

        $suggestArticles = Article::query()
            ->with(['user', 'category'])
            ->where(function ($query) use ($visitedArticlesCategoryIds, $visitedArticlesAuthorIds){
                $query->whereIn('category_id', $visitedArticlesCategoryIds)
                      ->orWhereIn('user_id', $visitedArticlesAuthorIds);

            })
            ->whereNotIn('id', $visitedArticles)
            ->limit(6)
            ->get();


        $userLike = $article
            ->articleLikes
            ->where('article_id', $article->id)
            ->where('user_id', \auth()->id())
            ->first();

        $article->increment('view_count');
        $article->save();

        return view('front.article-detail', compact('article', 'userLike', 'suggestArticles'));
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

    public function authorArticles(Request $request, string $username)
    {
        $articles = Article::query()
            ->with(['user:id,name,username', 'category:id,name,slug'])
            ->whereHas('user', function ($query) use ($username) {
                $query->where('username', $username);
            })
            ->paginate(21);

        $title = User::query()->where('username', $username)->first()->name.' Məqalələri';

        return view('front.article-list', compact( 'articles', 'title'));
    }

    public function search(Request $request)
    {
        $searchText = $request->q;

        $articles = Article::query()
            ->with(['user', 'category'])
            ->whereHas('user', function($query) use ($searchText)
            {
                $query->where('name', 'LIKE', "%" . $searchText . "%")
                    ->orWhere('username', 'LIKE', "%" . $searchText . "%")
                    ->orWhere('about', 'LIKE', "%" . $searchText . "%");
            })
            ->whereHas('category', function($query) use ($searchText)
            {
                $query->orWhere('name', 'LIKE', "%" . $searchText . "%")
                    ->orWhere('description', 'LIKE', "%" . $searchText . "%")
                    ->orWhere('slug', 'LIKE', "%" . $searchText . "%");
            })
            ->orWhere("title", "LIKE", "%" . $searchText . "%")
            ->orWhere("slug", "LIKE", "%" . $searchText . "%")
            ->orWhere("body", "LIKE", "%" . $searchText . "%")
            ->orWhere("tags", "LIKE", "%" . $searchText . "%")
            ->paginate(30);


        $title = $searchText.' Axtarış Nəticəsi';

        return view('front.article-list', compact( 'articles', 'title'));
    }

    public function articleList()
    {
        $articles = Article::query()->where('publish_date', '<=', now())->orderBy('publish_date', 'DESC')->paginate(21);

        return view('front.article-list', compact( 'articles'));
    }
}

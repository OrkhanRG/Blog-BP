<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Category;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        Cache::forget('most_popular_categories');
//        $categoryNames = Cache::get('most_popular_categories');
//        if (!Cache::has('most_popular_categories'))
//        {
//            $mostPopularCategories = Article::query()
//                ->with('category:id,name,slug,description,created_at,image')
//                ->whereHas('category', function ($query) {
//                    $query->where('status', 1);
//                })
//                ->orderBy('view_count', 'DESC')
//                ->groupBy()
//                ->get();
//
//            $categoryNames = [];
//            $mostPopularCategories->map(function ($item) use (&$categoryNames) {
//                if (count($categoryNames) < 4)
//                    $categoryNames[] = $item->category;
//            });
//            Cache::put('most_popular_categories', $categoryNames, 60);
//        }
        $categoryNames = Cache::remember('most_popular_categories', 3600, function (){
            $mostPopularCategories = Article::query()
                ->with('category:id,name,slug,description,created_at,image,color')
                ->whereHas('category', function ($query) {
                    $query->where('status', 1)
                        ->where('feature_status', 1);
                })
                ->orderBy('view_count', 'DESC')
                ->groupBy('category_id')
                ->get();


            $categoryNames = [];
            $mostPopularCategories->map(function ($item) use (&$categoryNames) {
                if (count($categoryNames) < 4)
                    $categoryNames[] = $item->category;
            });

            return $categoryNames;
        });

        $mostPopularArticles = Cache::remember('most_popular_articles', 3600, function (){
            return Article::query()
                ->with(['user', 'category'])
                ->whereHas('user')
                ->whereHas('category')
                ->orderBy('view_count', 'DESC')
                ->limit(6)
                ->get();
        });

        $lastPublishedArticles = Article::query()
            ->with('user', 'category')
            ->status(1)
            ->whereHas('user')
            ->whereHas('category')
            ->orderBy('publish_date', 'DESC')
            ->limit(6)
            ->get();

        return view('front.index', ['mostPopularCategories' => $categoryNames], compact('mostPopularArticles', 'lastPublishedArticles'));
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

        $category = Category::query()->where('slug', $slug)->first();
        $title = $category->name . ' Kateqoriyasına Aid Məqalələr';

        return view('front.article-list', compact( 'articles', 'title', 'category'));
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

        return view('front.article-detail',
            compact('article', 'userLike', 'suggestArticles'));
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
        $articles = Article::query()->where('publish_date', '<=', now())->orderBy('publish_date', 'DESC')->paginate(9);

        return view('front.article-list', compact( 'articles'));
    }
}

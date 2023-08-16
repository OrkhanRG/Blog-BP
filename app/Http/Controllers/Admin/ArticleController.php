<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleCreateRequest;
use App\Http\Requests\ArticleFilterRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\UserLikeArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{

    public function index(ArticleFilterRequest $request)
    {
        $users = User::all();
        $categories = Category::all();

        $list = Article::query()
            ->with(['category', 'user'])
            ->where(function($query) use ($request){
                $query->orWhere("title", "LIKE", "%" . $request->search_text . "%")
                    ->orWhere("slug", "LIKE", "%" . $request->search_text . "%")
                    ->orWhere("body", "LIKE", "%" . $request->search_text . "%")
                    ->orWhere("tags", "LIKE", "%" . $request->search_text . "%");
            })
            ->status($request->status)
            ->category($request->category_id)
            ->user($request->user_id)
            ->publishDate($request->publish_date)
            ->where(function ($query) use ($request){
                if ($request->min_view_count)
                {
                   $query->where('view_count', '>=', (int)$request->min_view_count);
                }

                if ($request->max_view_count)
                {
                    $query->where('view_count', '<=', (int)$request->max_view_count);
                }

                if ($request->min_like_count)
                {
                    $query->where('like_count', '>=', (int)$request->min_like_count);
                }

                if ($request->max_like_count)
                {
                    $query->where('like_count', '<=', (int)$request->max_like_count);
                }
            })
            ->paginate(5);

        return view("admin.articles.list", compact("users", "categories", "list"));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.articles.create-update', compact('categories'));
    }

    public function store(ArticleCreateRequest $request)
    {
        if (!is_null($request->image))
        {
            $imageFile = $request->file('image');
            $orginalName = $imageFile->getClientOriginalName();
            $orginalExtension = $imageFile->getClientOriginalExtension();
            $explodeName = explode('.', $orginalName)[0];
            $fileName = Str::slug($explodeName) . '.' . $orginalExtension;

            $folder = 'articles';
            $publicPath = 'storage/' . $folder;

            if (file_exists(public_path($publicPath . '/' . $fileName))) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'image' => 'Eyni şəkil daha əvəvl yüklənib'
                    ]);
            }
        }

        $data = $request->except('_token');
        $slug = $data['slug'] ?? $data['title'];
        $slug = Str::slug($slug);
        $slugTitle = Str::slug($data['title']);

        $checkSlug = $this->slugCheck($slug);

        if (!is_null($checkSlug)) {
            $checkTitleSlug = $this->slugCheck($slugTitle);
            if (!is_null($checkTitleSlug)) {
                $slug = Str::slug($slug . time());
            } else {
                $slug = $slugTitle;
            }
        }

        $data['slug'] = $slug;

        if (!is_null($request->image)) {
            $data['image'] = $publicPath . '/' . $fileName;
        }

        $data['user_id'] = auth()->id();
//        $data['user_id'] = auth()->user()->id;

        Article::create($data);
        if (!is_null($request->image))
        {
            $imageFile->storeAs($folder, $fileName);
        }
        alert()->success("Uğurlu", "Məqalə yükləndi")->showConfirmButton('yaxşı', '#3085d6')->autoClose(5000);
        return redirect()->back();

    }

    public function edit(Request $request, int $articleID)
    {
        $categories = Category::all();

//        $article = Article::where('id', $articleID)->firstOrFail();
        $article = Article::query()
                            ->where('id', $articleID)
                            ->first();

        if (is_null($article)) {
            $statusText = 'Kateqoriya Tapılmadı';
            alert()
                ->error("Xəta", $statusText)
                ->showConfirmButton('yaxşı', '#3085d6')
                ->autoClose(5000);

            return redirect()->route('article.index');
        }

        return view('admin.articles.create-update', compact('article', 'categories'));
    }

    public function update(ArticleUpdateRequest $request)
    {
        $data = $request->except('_token');
        $slug = $data['slug'] ?? $data['title'];
        $slug = Str::slug($slug);
        $slugTitle = Str::slug($data['title']);

        $checkSlug = $this->slugCheck($slug);

        if (!is_null($checkSlug)) {
            $checkTitleSlug = $this->slugCheck($slugTitle);
            if (!is_null($checkTitleSlug)) {
                $slug = Str::slug($slug . time());
            } else {
                $slug = $slugTitle;
            }
        }

        $data['slug'] = $slug;

        if (!is_null($request->image))
        {
            $imageFile = $request->file('image');
            $orginalName = $imageFile->getClientOriginalName();
            $orginalExtension = $imageFile->getClientOriginalExtension();
            $explodeName = explode('.', $orginalName)[0];
            $fileName = Str::slug($explodeName) . '.' . $orginalExtension;

            $folder = 'articles';
            $publicPath = 'storage/' . $folder;

            if (file_exists(public_path($publicPath . '/' . $fileName))) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'image' => 'Eyni şəkil daha əvəvl yüklənib'
                    ]);
            }

            $data['image'] = $publicPath . '/' . $fileName;
        }

        $data['user_id'] = auth()->id();

        $articleQuery = Article::query()
                                ->where('id', $request->id);
        $articleFind = $articleQuery->first();

        $articleQuery->first()->update($data);

        if (!is_null($request->image))
        {
            if (file_exists(public_path($articleFind->image)))
            {
                \File::delete($articleFind->image);
            }

            $imageFile->storeAs($folder, $fileName);
        }

        alert()->success("Uğurlu", "Məqalə Dəyişdirildi")->showConfirmButton('yaxşı', '#3085d6')->autoClose(5000);
        return redirect()->route('article.index');
    }

    public function slugCheck(string $text)
    {
        return Category::where('slug', Str::slug($text))->first();
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $articleID = $request->articleID;

        $article = Article::query()
            ->where('id',$articleID)
            ->first();

        if ($article)
        {
            $article->status = $article->status ? 0 : 1;
            $article->save();

            return response()
                ->json(['status' => 'success', 'message' => 'Uğurlu', 'data' => $article, 'article_status' => $article->status])
                ->setStatusCode(200);
        }

        return response()
            ->json(['status' => 'error', 'message' => 'Məqalə tapılmadı'])
            ->setStatusCode(404);
    }

    public function delete(Request $request): JsonResponse
    {
        $articleID = $request->articleID;

        $article = Article::query()
            ->where('id', $articleID)
            ->first();

        if ($article)
        {
            $article->delete();

            return response()
                ->json(['status' => 'success', 'message' => 'Məqalə silindi', 'data' => ''])
                ->setStatusCode(200);
        }

        return response()
            ->json(['status' => 'error', 'message' => 'Məqalə tapılmadı'])
            ->setStatusCode(404);
    }

    public function favorite(Request $request): JsonResponse
    {
        $article = Article::query()->with(['articleLikes' => function ($query) {
           $query->where('user_id', auth()->id());
        }
        ])->where('id', $request->id)->firstOrFail();

        if ($article->articleLikes->count())
        {
            $article->articleLikes()->delete();
            $article->like_count--;
            $process = 0;
        }
        else
        {
            UserLikeArticle::create([
                'user_id' => auth()->id(),
                'article_id' => $request->id
            ]);
            $article->like_count++;
            $process = 1;
        }

        $article->save();

        return response()
            ->json(['status' => 'success',
                'message' => 'Uğurlu',
                'like_count' => $article->like_count,
                'process' => $process
            ])
            ->setStatusCode(200);
    }
}

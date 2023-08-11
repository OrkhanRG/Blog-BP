<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PharIo\Version\Exception;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $parentID = $request->parent_id;
        $userID = $request->user_id;

        $parentCategory = Category::all();
        $user = User::all();

        $categories = Category::with(['parentCategory', 'user'])
            ->where(function ($query) use ($parentID, $userID) {
                if (!is_null($parentID)) {
                    return $query->where('parent_id', $parentID);
                }

                if (!is_null($userID)) {
                    return $query->where('user_id', $userID);
                }
            })
            ->name($request->name)
            ->slug($request->slug)
            ->description($request->description)
            ->order($request->order)
            ->status($request->status)
            ->featureStatus($request->feature_status)
            ->orderBy('order', 'DESC')
            ->paginate(5);

        return view('admin.categories.list', [
            'list' => $categories,
            'parentCategories' => $parentCategory,
            'users' => $user
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create-update', compact('categories'));
    }

    public function store(CategoryStoreRequest $request)
    {
        $slug = Str::slug($request->slug);

        try {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = is_null($this->slugCheck($slug)) ? $slug : Str::slug($slug . time());
            $category->color = $request->color;
            $category->description = $request->description;
            $category->status = $request->status ? 1 : 0;
            $category->parent_id = $request->parent_id;
            $category->feature_status = $request->feature_status ? 1 : 0;
            $category->seo_keywords = $request->seo_keywords;
            $category->seo_description = $request->seo_description;
            $category->user_id = auth()->id();
            $category->order = $request->order;


            if (!is_null($request->image)) {
                $imageFile = $request->file('image');
                $orginalName = $imageFile->getClientOriginalName();
                $orginalExtension = $imageFile->getClientOriginalExtension();
                $explodeName = explode('.', $orginalName)[0];
                $fileName = Str::slug($explodeName) . '.' . $orginalExtension;

                $folder = 'categories';
                $publicPath = 'storage/' . $folder;

                if (file_exists(public_path($publicPath . '/' . $fileName))) {
                    return redirect()
                        ->back()
                        ->withErrors([
                            'image' => 'Eyni şəkil daha əvəvl yüklənib'
                        ]);
                }

                $category->image = $publicPath . '/' . $fileName;
                $imageFile->storeAs($folder, $fileName);
            }

            $category->save();

        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }


        alert()->success("Uğurlu", "Kateqoriya əlavə edildi")->showConfirmButton('yaxşı', '#3085d6')->autoClose(5000);
        return redirect()->back();
    }

    public function slugCheck(string $text)
    {
        return Category::where('slug', Str::slug($text))->first();
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:categories']
        ]);

        $categoryID = $request->id;

        $category = Category::where('id', $categoryID)->first();

        $oldStatus = $category->status;

        $category->status = !$category->status;
        $category->save();

        $statusText = ($oldStatus == 1 ? 'Aktiv' : 'Passiv') . "'dən " . ($category->status == 1 ? 'Aktiv' : 'Passiv');

        alert()
            ->success("Uğurlu", $category->name . " status " . $statusText . "'ə dəyişdirildi")
            ->showConfirmButton('yaxşı', '#3085d6')
            ->autoClose(5000);

        return redirect()->route('category.index');
    }

    public function changeFeatureStatus(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:categories']
        ]);

        $categoryID = $request->id;

        $category = Category::where('id', $categoryID)->first();

        $oldStatus = $category->feature_status;

        $category->feature_status = !$category->feature_status;
        $category->save();

        $statusText = ($oldStatus == 1 ? 'Aktiv' : 'Passiv') . "'dən " . ($category->feature_status == 1 ? 'Aktiv' : 'Passiv');

        alert()
            ->success("Uğurlu", $category->name . " feature status " . $statusText . "'ə dəyişdirildi")
            ->showConfirmButton('yaxşı', '#3085d6')
            ->autoClose(5000);

        return redirect()->route('category.index');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:categories']
        ]);

        $categoryID = $request->id;

        $category = Category::where('id', $categoryID)->delete();

        $statusText = 'Kateqoriya Silindi';

        alert()
            ->success("Uğurlu", $statusText)
            ->showConfirmButton('yaxşı', '#3085d6')
            ->autoClose(5000);

        return redirect()->route('category.index');
    }

    public function edit(Request $request)
    {
        $categories = Category::all();
        $categoryID = $request->id;

        $category = Category::where('id', $categoryID)->first();

        if (is_null($category)) {
            $statusText = 'Kateqoriya Tapılmadı';
            alert()
                ->error("Xəta", $statusText)
                ->showConfirmButton('yaxşı', '#3085d6')
                ->autoClose(5000);

            return redirect()->route('category.index');
        }

        return view('admin.categories.create-update', compact('category', 'categories'));
    }

    public function update(CategoryStoreRequest $request)
    {
        $slug = Str::slug($request->slug);
        $slugCheck = $this->slugCheck($slug);

/*        $categoryQuery = Category::query()
            ->where('id', $request->id);
        $categoryFind = $categoryQuery->first();*/

        $category = Category::find($request->id);
        $category->name = $request->name;

        if ((!is_null($slugCheck) && $slugCheck->id == $category->id) || is_null($slugCheck)) {
            $category->slug = $slug;
        } else if (!is_null($slugCheck) && $slugCheck->id != $category->id) {
            $category->slug = Str::slug($slug . time());
        } else {
            $category->slug = Str::slug($slug . time());
        }

        $category->color = $request->color;
        $category->description = $request->description;
        $category->status = $request->status ? 1 : 0;
        $category->parent_id = $request->parent_id;
        $category->feature_status = $request->feature_status ? 1 : 0;
        $category->seo_keywords = $request->seo_keywords;
        $category->seo_description = $request->seo_description;
//        $category->user_id = random_int(1, 10);
        $category->order = $request->order;

        if (!is_null($request->image)) {
            $imageFile = $request->file('image');
            dd($imageFile);
            $orginalName = $imageFile->getClientOriginalName();
            $orginalExtension = $imageFile->getClientOriginalExtension();
            $explodeName = explode('.', $orginalName)[0];
            $fileName = Str::slug($explodeName) . '.' . $orginalExtension;

            $folder = 'categories';
            $publicPath = 'storage/' . $folder;

            if (file_exists(public_path($publicPath . '/' . $fileName))) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'image' => 'Eyni şəkil daha əvəvl yüklənib'
                    ]);
            }

            \File::delete($category->image);

            $category->image = $publicPath . '/' . $fileName;

            $imageFile->storeAs($folder, $fileName);
        }

        $category->save();

        alert()->success("Uğurlu", "Kateqoriya güncəlləndi")->showConfirmButton('yaxşı', '#3085d6')->autoClose(5000);
        return redirect()->route('category.index');
    }
}

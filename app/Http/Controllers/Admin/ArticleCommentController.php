<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleComment;
use App\Models\User;
use App\Models\UserLikeComment;
use App\Traits\Loggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    use Loggable;
    public function approvalList(Request $request)
    {
        $users = User::all();
        $comments = ArticleComment::query()
            ->with(['user', 'article', 'children', 'article.user'])
            ->approveStatus()
            ->user($request->user_id)
            ->createdDate($request->created_at)
            ->searchText($request->search_text)
            ->paginate(10);

        $page = 'approval';

        return view('admin.articles.comment-list', compact('comments', 'users', 'page'));
    }

    public function list(Request $request)
    {
        $users = User::all();
        $comments = ArticleComment::query()
            ->withTrashed()
            ->with(['user', 'article', 'children', 'article.user'])
            ->status($request->status)
            ->user($request->user_id)
            ->createdDate($request->created_at)
            ->searchText($request->search_text)
            ->paginate(10);

        $page = 'commentList';

        return view('admin.articles.comment-list', compact('comments', 'users', 'page'));
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $page = $request->page;

        $comment = ArticleComment::findOrFail($id);

        if ($page == "approval")
        {
            $comment->approve_status = 1;
        }
        else
        {
            $comment->status = $comment->status ? 0 : 1;
        }

        $this->updateLog($comment, ArticleComment::class);
        $comment->save();

        return response()
            ->json(['status' => 'success', 'message' => 'Uğurlu', 'data' => $comment, 'comment_status' => $comment->status])
            ->setStatusCode(200);

    }

    public function delete(Request $request)
    {
        $comment = ArticleComment::findOrFail($request->id);

        $this->log('delete', $comment->id, $comment->toArray(), ArticleComment::class);
        $comment->delete();

        return response()
            ->json([
                'status' => 'success',
                'message' => 'Uğurlu',
                'data' => $comment])
            ->setStatusCode(200);
    }

    public function restore(Request $request): JsonResponse
    {
        $comment = ArticleComment::withTrashed()->findOrFail($request->id);
        $comment->restore();

        return response()
            ->json(['status' => 'success',
                    'message' => 'Komment geri gətirildi',
                    'data' => $comment])
            ->setStatusCode(200);
    }

    public function favorite(Request $request): JsonResponse
    {
        $comment = ArticleComment::query()
            ->with(['commentLikes' => function ($query) {
                 $query->where('user_id', auth()->id());
             }])
            ->where('id', $request->id)
            ->firstOrFail();

        if ($comment->commentLikes->count())
        {
            $comment->commentLikes()->delete();
            $comment->like_count--;
            $process = 0;
        }
        else
        {
            UserLikeComment::create([
                'user_id' => auth()->id(),
                'comment_id' => $request->id
            ]);
            $comment->like_count++;
            $process = 1;
        }

        $comment->save();

        return response()
            ->json(['status' => 'success',
                'message' => 'Uğurlu',
                'like_count' => $comment->like_count,
                'process' => $process
            ])
            ->setStatusCode(200);
    }
}

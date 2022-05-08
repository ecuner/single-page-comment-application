<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Pagination\Paginator;

class CommentController extends Controller
{
    const COMMENTS_PER_PAGE = 15;
    const NEWEST_COMMENT_FIRST = true;
    const NESTED_COMMENTS_LAYER_LIMIT = 3;

    /**
     * Show comments, paginated and sorted (plus a hardcoded blog post).
     * 
     * paginate() detects current page by "page" query string argument,
     * but since we will use Vue pagination, it will mostly go unused.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('comment.index', [
            'comments' => Comment::allForFrontend(self::COMMENTS_PER_PAGE, self::NEWEST_COMMENT_FIRST),
            'layerLimit' => self::NESTED_COMMENTS_LAYER_LIMIT
        ]);
    }

    /**
     * Returns paginator object for comments in JSON, without total/page list.
     * 
     * paginate() detects current page by "page" query string argument.
     * Used by Vue.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJson() {
        return response()->json(Comment::allForFrontend(self::COMMENTS_PER_PAGE, self::NEWEST_COMMENT_FIRST, true));
    }

    /**
    * Store a comment, and get comments on the page new comment is located in.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
            'parent_id' => 'nullable|numeric|gt:-1',
        ]);

        if (!$validated["parent_id"])
            $validated["parent_id"] = null;
        else {
            $layers = Comment::findOrFail($validated["parent_id"])->ancestors()->count();
            if ($layers >= self::NESTED_COMMENTS_LAYER_LIMIT)
                return response()->json(['error' => 'We don\'t allow too many nested comments, sorry.'], 500);
        }

        $comment = new Comment();
        $comment->fill($validated);
        if (!$comment->save())
            return response()->json(['error' => 'Comment couldn\'t be saved, please try again.'], 500);
        
        // New comments either go to end or beginning, so let's help the client
        // by sending the comments on that page.
        // On subcomment, paginate() will use "page" value from request.
        if (!$validated["parent_id"]) {
            Paginator::currentPageResolver(function() {
                return CommentController::NEWEST_COMMENT_FIRST ? 1 :
                    Comment::lastPage(CommentController::COMMENTS_PER_PAGE);
            });
            $results = Comment::allForFrontend(self::COMMENTS_PER_PAGE, self::NEWEST_COMMENT_FIRST)->toArray();
        } else {
            // We're passing true to third parameter, since we don't need to calculate pages again.
            $results = Comment::allForFrontend(self::COMMENTS_PER_PAGE, self::NEWEST_COMMENT_FIRST, true)->toArray();
        }

        $results['new_comment_id'] = $comment->id;
        return response()->json($results);
    }
}

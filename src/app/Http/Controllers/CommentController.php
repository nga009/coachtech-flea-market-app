<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\CommentRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CommentController extends Controller
{
    // コメント登録
    public function store(CommentRequest $request, $item_id)
    {
        Log::info('Ajax request received--CommentController::store start ' . $item_id);

        $item = Item::find($item_id);        

        $comment = $item->comments()->create([
            'item_id' => $item_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        Log::info('Ajax request received--CommentController::store after create ' . $comment->user->profile);

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user_name' => $comment->user->name,
                'profile_image' => $comment->user->profile->profile_image,
            ],
            'comments_count' => $item->commentsCount(),
        ]);
    }
}

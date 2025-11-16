<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;


class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        $user = auth()->user();

        // コメント保存
        $comment = Comment::create([
            'item_id' => $itemId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);
        // リレーションロード
        $comment->load('user', 'item');

        // プロフィール画像
        $profileImage = ($comment->user->profile_image && file_exists(storage_path('app/public/' . $comment->user->profile_image)))
            ? asset('storage/' . $comment->user->profile_image)
            : asset('images/profile_images/default-profile.png');

        return response()->json([
            'status' => 'success',
            'comment' => [
                'id' => $comment->id,
                'user_name' => $comment->user->name,
                'profile_image' =>$profileImage,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->diffForHumans(),
            ],
            'comment_count' => $comment->item->comments()->count(),
        ]);
    }
}
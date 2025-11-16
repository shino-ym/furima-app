<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegisterRequest;

class CustomRegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        // ユーザー登録処理
        $user = app(CreateNewUser::class)->create($request->validated());

        // 登録完了イベントを発火
        event(new Registered($user));

        // メール認証通知ページへリダイレクト
        return redirect()->route('verification.notice');
    }
}

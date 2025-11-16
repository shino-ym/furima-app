<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // メール未認証なら verify ページへ
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->isFirstLogin ?? false) {
            // 初回ログイン → プロフィール設定画面
            $user->isFirstLogin = false;
            $user->save();
            return redirect()->route('mypage.profile.edit');
        }

        // それ以外 → インデックス画面
        return redirect()->route('index');
    }
}


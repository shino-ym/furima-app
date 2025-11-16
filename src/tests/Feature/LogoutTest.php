<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログアウトができる()
    {
        // ユーザーを作成してログイン状態にする
        $user = User::factory()->create();
        $this->actingAs($user);
        // ログアウトボタンを押す
        $response = $this->post('/logout');
        // ログアウト後はトップページにリダイレクト
        $response->assertRedirect(route('index'));
        // セッションが破棄され認証状態ではないことを確認
        $this->assertGuest();
    }
}
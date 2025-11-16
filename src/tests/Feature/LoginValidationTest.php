<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginValidationTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function メールアドレスが入力されていない場合、「メールアドレスを入力してください」というバリデーションメッセージが表示される ()
    {
        // ログインページを開く
        $response = $this->get('/login');

        // メールアドレスを入力せずに他の必要項目を入力する
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        // email に対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['email']);

        $response = $this->get('/login');
        $response->assertSee('メールアドレスを入力してください');
    }

    /** @test */
    public function メールアドレスがメール形式以外だと「メールアドレスはメール形式で入力してください」というバリデーションメッセージが表示される()
    {
        $response = $this->get('/login');

        $response = $this->post('/login', [
            'email' => 'abc123',
            'password' => 'password123',
        ]);

        // email に対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['email']);

        $response = $this->get('/login');
        $response->assertSee('メールアドレスはメール形式で入力してください');
    }

    /** @test */
    public function パスワードが未入力だと「パスワードを入力してください」というバリデーションメッセージが表示される()
    {
        $response = $this->get('/login');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        // パスワードに対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['password']);

        $response = $this->get('/login');
        $response->assertSee('パスワードを入力してください');
    }

        /** @test */
    public function 入力情報が間違っている場合、「ログイン情報が登録されていません。」というバリデーションメッセージが表示される()
    {
        $response = $this->get('/login');

        $this->withExceptionHandling();
        // 存在しないユーザーの情報。どのページからアクセスしようとしたかをテスト用に設定
        $response = $this->from('/login')->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);
        // セッションのエラー情報を取得
        $errors = session('errors');
        // ログイン失敗はログイン画面に戻ることを確認
        $response->assertRedirect('/login');

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('ログイン情報が登録されていません。',$errors->first('login_error'));
    }

    /** @test */
    public function 正しい情報が入力された場合、ログイン処理が実行される()
    {
        // テストユーザーを作成
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // バリデーションエラーがないことを確認
        $response->assertSessionDoesntHaveErrors();

        // トップページにリダイレクトされることを確認
        $response->assertRedirect(route('index'));

        // ユーザーが認証済みになっていることを確認
        $this->assertAuthenticatedAs($user);
    }
}

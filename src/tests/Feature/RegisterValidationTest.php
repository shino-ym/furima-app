<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が未入力だとバリデーションエラーになる()
    {
        $this->get('/register');

        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // name に対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseMissing('users',['email'=>'test@example.com']);

        $response = $this->get('/register');
        $response->assertSee('お名前を入力してください');

    }

    /** @test */
    public function メールアドレスが未入力だとバリデーションエラーになる()
    {
        $this->get('/register');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // email に対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users',['name'=>'テストユーザー']);

        $response = $this->get('/register');
        $response->assertSee('メールアドレスを入力してください');
    }

    /** @test */
    public function メールアドレスがメール形式以外だとバリデーションエラーになる()
    {
        $this->get('/register');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'abc123',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // email に対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users',['email'=>'abc123']);

        $response = $this->get('/register');
        $response->assertSee('メールアドレスはメール形式で入力してください');
    }

    /** @test */
    public function パスワードが未入力だとバリデーションエラーになる()
    {
        $this->get('/register');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);

        // パスワードに対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users',['email'=>'test@example.com']);

        $response = $this->get('/register');
        $response->assertSee('パスワードを入力してください');
    }

    /** @test */
    public function パスワードが7文字以下だとバリデーションエラーになる()
    {
        $this->get('/register');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        // パスワードに対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users',['email'=>'test@example.com']);

        $response = $this->get('/register');
        $response->assertSee('パスワードは8文字以上で入力してください');
    }

        /** @test */
    public function パスワードとパスワード確認が不一致だとバリデーションエラーになる()
    {
        $this->get('/register');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password333',
        ]);

        // パスワードに対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users',['email'=>'test@example.com']);

        $response = $this->get('/register');
        $response->assertSee('パスワードと一致しません');
    }

    /**
     * テストケースでは「会員情報が正しく入力されている場合はプロフィール設定画面に遷移される」ということだが、メール認証機能を装着しため、メール認証画面に遷移に変更
     */

    /** @test */
    public function 会員情報が登録され、メール認証画面に遷移する()
    {
        $this->get('/register');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // バリデーションエラーがないことを確認
        $response->assertSessionDoesntHaveErrors();

        // DBに登録されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        // メール認証ページにリダイレクトされることを確認
        $response->assertRedirect(route('verification.notice'));
    }
}

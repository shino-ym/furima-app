<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserInfoUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーの変更項目が初期値として過去設定されている()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile.edit'));

        $response -> assertStatus(200)
                ->assertSee($user->name)
                ->assertSee($user->profile_image ?? 'default-profile.png')
                ->assertSee($user->default_postal_code)
                ->assertSee($user->default_address_line)
                ->assertSee($user->default_building);
    }
}

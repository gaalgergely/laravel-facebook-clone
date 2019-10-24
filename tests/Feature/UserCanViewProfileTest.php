<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCanViewProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_user_profiles()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $posts = factory(\App\Post::class)->create();

        $response = $this->get('/api/users/'.$user->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'user_id' => $user->id,
                    'attributes' => [
                        'name' => $user->name,
                    ]
                ],
                'links' => [
                    'self' => url('/users/'.$user->id),
                ]
            ]);
    }
}

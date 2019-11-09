<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_friend_request()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();

        $response = $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $friendRequest = \App\Friend::first();

        $this->assertNotNull($friendRequest);
        $this->assertEquals($anotherUser->id, $friendRequest->friend_id);
        $this->assertEquals($user->id, $friendRequest->user_id);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null,
                ]
            ],
            'links' => [
                'self' => url('/users/'.$anotherUser->id),
            ]
        ]);
    }

    /** @test */
    public function a_user_can_send_a_friend_request_only_once()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $friendRequests = \App\Friend::all();
        $this->assertCount(1, $friendRequests);
    }

    /** @test */
    public function only_valid_users_can_be_friend_requested()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');

        $response = $this->post('/api/friend-request', [
            'friend_id' => 123,
        ])->assertStatus(404);

        $this->assertNull(\App\Friend::first());
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'User Not Found',
                'detail' => 'Unable to locate the user with the given information.',
            ]
        ]);
    }

    /** @test */
    public function friend_requests_can_be_accepted()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 1,
            ])->assertStatus(200);

        $friendRequest = \App\Friend::first();
        $this->assertNotNull($friendRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
        $this->assertEquals(1, $friendRequest->status);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => $friendRequest->confirmed_at->diffForHumans(),
                    'friend_id' => $friendRequest->friend_id,
                    'user_id' => $friendRequest->user_id,
                ]
            ],
            'links' => [
                'self' => url('/users/'.$anotherUser->id),
            ]
        ]);
    }

    /** @test */
    public function friend_requests_can_be_ignored()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($anotherUser, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,
            ])->assertStatus(204);

        $friendRequest = \App\Friend::first();
        $this->assertNull($friendRequest);
        $response->assertNoContent();
    }

    /** @test */
    public function only_valid_friend_requests_can_be_accepted()
    {
        $anotherUser = factory(\App\User::class)->create();

        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => 123,
                'status' => 1,
            ])->assertStatus(404);

        $this->assertNull(\App\Friend::first());
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information.',
            ]
        ]);
    }

    /** @test */
    public function only_the_recipient_can_accept_a_friend_request()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs(factory(\App\User::class)->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 1,
            ])->assertStatus(404);

        $friendRequest = \App\Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information.',
            ]
        ]);
    }

    /** @test */
    public function only_the_recipient_can_ignore_a_friend_request()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs(factory(\App\User::class)->create(), 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,
            ])->assertStatus(404);

        $friendRequest = \App\Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information.',
            ]
        ]);
    }

    /** @test */
    public function a_friend_id_is_required_for_friend_requests()
    {
        $response = $this->actingAs($user = factory(\App\User::class)->create(), 'api')
            ->post('/api/friend-request', [
                'friend_id' => '',
            ]);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('friend_id', $responseString['errors']['meta']);
    }

    /** @test */
    public function a_user_id_and_status_is_required_for_friend_request_responses()
    {
        $response = $this->actingAs($user = factory(\App\User::class)->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => '',
                'status' => '',
            ])->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
        $this->assertArrayHasKey('status', $responseString['errors']['meta']);
    }

    /** @test */
    public function a_user_id_is_required_for_ignoring_a_friend_request_responses()
    {
        $response = $this->actingAs($user = factory(\App\User::class)->create(), 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => '',
            ])->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
    }

    /** @test */
    public function a_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();
        $friendRequest = \App\Friend::create([
            'user_id' => $user->id,
            'friend_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1,
        ]);

        $this->get('/api/users/'.$anotherUser->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ],
            ]);
    }

    /** @test */
    public function an_inverse_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->actingAs($user = factory(\App\User::class)->create(), 'api');
        $anotherUser = factory(\App\User::class)->create();
        $friendRequest = \App\Friend::create([
            'friend_id' => $user->id,
            'user_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1,
        ]);

        $this->get('/api/users/'.$anotherUser->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ],
            ]);
    }
}

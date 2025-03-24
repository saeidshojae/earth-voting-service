<?php

namespace Tests\Feature\API;

use App\Models\GroupElection;
use App\Models\User;
use App\Models\Vote;
use App\Services\MainProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $election;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->election = GroupElection::factory()->create(['status' => 'active']);

        // Mock MainProjectService
        $this->mock(MainProjectService::class, function ($mock) {
            $mock->shouldReceive('getMembershipStatus')->andReturn('active');
        });
    }

    /** @test */
    public function user_can_see_their_votes()
    {
        Vote::factory()->count(3)->create([
            'voter_id' => $this->user->id,
            'group_election_id' => $this->election->id
        ]);

        $response = $this->getJson('/api/votes/my');

        $response->assertOk()
                ->assertJsonCount(3);
    }

    /** @test */
    public function user_can_cast_a_vote()
    {
        $candidate = User::factory()->create();
        
        $response = $this->postJson('/api/votes', [
            'group_election_id' => $this->election->id,
            'candidate_id' => $candidate->id,
            'position_type' => 'board_member'
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('votes', [
            'voter_id' => $this->user->id,
            'candidate_id' => $candidate->id,
            'position_type' => 'board_member'
        ]);
    }

    /** @test */
    public function user_cannot_vote_twice_for_same_candidate()
    {
        $candidate = User::factory()->create();
        
        Vote::create([
            'group_election_id' => $this->election->id,
            'voter_id' => $this->user->id,
            'candidate_id' => $candidate->id,
            'position_type' => 'board_member'
        ]);

        $response = $this->postJson('/api/votes', [
            'group_election_id' => $this->election->id,
            'candidate_id' => $candidate->id,
            'position_type' => 'board_member'
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_can_update_their_vote()
    {
        $vote = Vote::create([
            'group_election_id' => $this->election->id,
            'voter_id' => $this->user->id,
            'candidate_id' => User::factory()->create()->id,
            'position_type' => 'board_member'
        ]);

        $newCandidate = User::factory()->create();

        $response = $this->putJson("/api/votes/{$vote->id}", [
            'candidate_id' => $newCandidate->id
        ]);

        $response->assertOk();
        $this->assertEquals($newCandidate->id, $vote->fresh()->candidate_id);
    }

    /** @test */
    public function user_can_delete_their_vote()
    {
        $vote = Vote::create([
            'group_election_id' => $this->election->id,
            'voter_id' => $this->user->id,
            'candidate_id' => User::factory()->create()->id,
            'position_type' => 'board_member'
        ]);

        $response = $this->deleteJson("/api/votes/{$vote->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('votes', ['id' => $vote->id]);
    }
} 
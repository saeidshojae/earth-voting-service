<?php

namespace Tests\Feature\API;

use App\Models\User;
use App\Models\VoteDelegation;
use App\Services\MainProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoteDelegationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock MainProjectService
        $this->mock(MainProjectService::class, function ($mock) {
            $mock->shouldReceive('getMembershipStatus')->andReturn('active');
        });
    }

    /** @test */
    public function user_can_see_their_delegations()
    {
        VoteDelegation::factory()->count(3)->create([
            'delegator_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/delegations/my');

        $response->assertOk()
                ->assertJsonCount(3);
    }

    /** @test */
    public function user_can_see_received_delegations()
    {
        VoteDelegation::factory()->count(3)->create([
            'delegate_id' => $this->user->id,
            'is_active' => true
        ]);

        $response = $this->getJson('/api/delegations/received');

        $response->assertOk()
                ->assertJsonCount(3);
    }

    /** @test */
    public function user_can_create_delegation()
    {
        $delegate = User::factory()->create();
        
        $response = $this->postJson('/api/delegations', [
            'delegate_id' => $delegate->id,
            'expertise_area' => 'technical',
            'start_date' => now(),
            'end_date' => now()->addMonth()
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('vote_delegations', [
            'delegator_id' => $this->user->id,
            'delegate_id' => $delegate->id,
            'expertise_area' => 'technical'
        ]);
    }

    /** @test */
    public function user_cannot_delegate_twice_in_same_area()
    {
        $delegate = User::factory()->create();
        
        VoteDelegation::create([
            'delegator_id' => $this->user->id,
            'delegate_id' => $delegate->id,
            'expertise_area' => 'technical',
            'start_date' => now(),
            'is_active' => true
        ]);

        $response = $this->postJson('/api/delegations', [
            'delegate_id' => User::factory()->create()->id,
            'expertise_area' => 'technical',
            'start_date' => now()
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_can_update_delegation()
    {
        $delegation = VoteDelegation::create([
            'delegator_id' => $this->user->id,
            'delegate_id' => User::factory()->create()->id,
            'expertise_area' => 'technical',
            'start_date' => now(),
            'is_active' => true
        ]);

        $response = $this->putJson("/api/delegations/{$delegation->id}", [
            'end_date' => now()->addMonth(),
            'is_active' => false
        ]);

        $response->assertOk();
        $this->assertFalse($delegation->fresh()->is_active);
    }

    /** @test */
    public function user_can_delete_delegation()
    {
        $delegation = VoteDelegation::create([
            'delegator_id' => $this->user->id,
            'delegate_id' => User::factory()->create()->id,
            'expertise_area' => 'technical',
            'start_date' => now(),
            'is_active' => true
        ]);

        $response = $this->deleteJson("/api/delegations/{$delegation->id}");

        $response->assertOk();
        $this->assertFalse($delegation->fresh()->is_active);
    }
} 
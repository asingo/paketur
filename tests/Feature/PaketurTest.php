<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaketurTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        // Seed initial users
        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->manager = Manager::factory()->create();

        $this->userManager = User::factory()->create([
            'role' => 'manager',
        ]);

        $this->userEmployee = User::factory()->create([
            'role' => 'employee',
        ]);

        $this->employee = User::factory()->create();

        $this->company = Company::factory()->create();

    }

    /** @test */
    public function test_registration_endpoint()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'manager',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role']]);
        $this->manager = $response->json();
    }

    /** @test */
    public function test_login_endpoint()
    {
        $payload = [
            'email' => $this->superAdmin->email,
            'password' => 'password', // Assuming default password for factory
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /** @test */
    public function test_add_company_endpoint()
    {
        $payload = [
            'name' => 'New Company',
        ];

        $response = $this->actingAs($this->superAdmin, 'api')->postJson('/api/company/create', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['company' => ['id', 'name']]);
        $this->company = $response->json('company');

    }

    /** @test */
    public function test_update_company_endpoint()
    {
        $payload = [
            'name' => 'Updated Company Name',
        ];
        $response = $this->actingAs($this->superAdmin, 'api')->putJson("/api/company/update/{$this->company->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);
    }

    /** @test */
    public function test_delete_company_endpoint()
    {
        $response = $this->actingAs($this->superAdmin, 'api')->deleteJson("/api/company/delete/{$this->company->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('companies', ['id' => $this->company->id]);
    }

    /** @test */
    public function test_get_all_companies_endpoint()
    {
        $response = $this->actingAs($this->superAdmin, 'api')->getJson('/api/company');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_add_manager_endpoint()
    {
        $payload = [
            'name' => 'Manager Name',
            'email' => 'manager@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company' => $this->company->id,
            'phone_number' => '1234567890',
        ];

        $response = $this->actingAs($this->userManager, 'api')->postJson('/api/manager/create', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['manager' => ['id', 'company_id', 'phone_number']]);
    }

    /** @test */
    public function test_update_manager_endpoint()
    {
        $payload = [
            'name' => 'Updated Manager Name',
            'email' => 'updated_manager@gmail.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'company' => $this->company->id,
            'phone_number' => '1076543210',
        ];

        $response = $this->actingAs($this->userManager, 'api')->putJson("/api/manager/update/{$this->manager->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);
    }

    /** @test */
    public function test_employee_can_see_other_employees()
    {
        $otherEmployee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($this->userEmployee, 'api')->getJson('/api/employee/');

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);
    }
}

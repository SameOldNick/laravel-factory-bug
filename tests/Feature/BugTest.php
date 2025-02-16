<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;

class BugTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests creating model using UseFactory attribute and then creating a model using name guessing.
     *
     * @return void
     */
    public function test_attribute_nameguess(): void
    {
        try {
            // Works fine
            Role::factory(5)->create();

            // Will try insert into roles table
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        } catch (QueryException $e) {
            /**
             * This will fail with the following message:
             *  SQLSTATE[HY000]: General error: 1 table roles has no column named name (Connection: sqlite, SQL: insert into "roles" ("name", "email", "email_verified_at", "password", "remember_token", "updated_at", "created_at") values (Test User, test@example.com, 2025-02-16 20:47:20, $2y$12$BBUBrhSB/JiTSXmvQAcGyO/ZwXjGhCMQTaaz2HcZyMsMCcU8QrC3., wuk94nSjpx, 2025-02-16 20:47:21, 2025-02-16 20:47:21))
             */
            $this->fail('QueryException: ' . $e->getMessage());
        }
    }

    /**
     * Tests factories have different model names.
     *
     * @return void
     */
    public function test_model_names_different()
    {
        $roleFactory = Role::factory();
        $userFactory = User::factory();

        $this->assertNotEquals($roleFactory->modelName(), $userFactory->modelName());
    }
}

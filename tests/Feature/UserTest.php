<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase {

    /* Test listing all users.
    *
    * @return void
    */
    public function testIndex() {
        $users = User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment([
                'email' => $users[0]->email,
                'name' => $users[0]->name,
            ])
            ->assertJsonFragment([
                'email' => $users[1]->email,
                'name' => $users[1]->name,
            ])
            ->assertJsonFragment([
                'email' => $users[2]->email,
                'name' => $users[2]->name,
            ]);
    }

    /* Test creating a user.
    *
    * @return void
    */
    public function testStore() {
        $userData = [
            'name' => 'John',
            'surname' => 'Doe',
            'birth_date' => '1994-07-07',
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $userData['name'],
                'surname' => $userData['surname'],
                'email' => $userData['email'],
                'birth_date' => $userData['birth_date'],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'surname' => $userData['surname'],
            'email' => $userData['email'],
            'birth_date' => $userData['birth_date'],
        ]);
    }

    /* Test retrieving a user.
    *
    * @return void
    */
    public function testShow() {
        $user = User::factory()->create();

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'birth_date' => $user->birth_date,
                    'email' => $user->email,
                ],
            ]);
    }

    /* Test updating a user.
    *
    * @return void
    */
    public function testUpdate() {
        $user = User::factory()->create();

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ];

        $response = $this->putJson('/api/users/' . $user->id, $userData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test deleting a user.
     *
     * @return void
     */
    public function testDestroy() {
        $user = User::factory()->create();

        $response = $this->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}

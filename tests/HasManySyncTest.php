<?php

namespace Korridor\LaravelHasManySync\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Korridor\LaravelHasManySync\Tests\TestEnvironment\Models\Task;
use Korridor\LaravelHasManySync\Tests\TestEnvironment\Models\User;

class HasManySyncTest extends TestCase
{
    use RefreshDatabase;

    private function createTwoUsersWithTwoTasksEach()
    {
        Model::unguard();
        User::create([
            'id' => 1,
            'name' => 'Tester 1',
        ]);
        User::create([
            'id' => 2,
            'name' => 'Tester 2',
        ]);

        Task::create([
            'id' => 1,
            'user_id' => 1,
            'content' => 'Task 1 of Tester 1',
        ]);
        Task::create([
            'id' => 2,
            'user_id' => 1,
            'content' => 'Task 2 of Tester 1',
        ]);
        Task::create([
            'id' => 3,
            'user_id' => 2,
            'content' => 'Task 1 of Tester 2',
        ]);
        Task::create([
            'id' => 4,
            'user_id' => 2,
            'content' => 'Task 2 of Tester 2',
        ]);
        Model::reguard();
    }

    public function testHasManySyncCreatesUpdatesAndDeletesIfDeletingIsActivated()
    {
        // Arrange
        $this->createTwoUsersWithTwoTasksEach();
        $user1 = User::find(1);
        $user2 = User::find(2);

        // Act
        $user1->tasks()->sync([
            // Create
            [
                'id' => null,
                'content' => 'Tasks 3 of Tester 1',
            ],
            // Update
            [
                'id' => 2,
                'content' => 'Updated Task 2 of Tester 1',
            ],
            // Delete, because task with id=1 is missing
        ], true);

        // Assert
        $this->assertEquals(4, Task::count());
        $this->assertEquals(5, Task::withTrashed()->count());
        $this->assertDatabaseHas('tasks', [
            'content' => 'Tasks 3 of Tester 1',
            'user_id' => 1,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 1,
            'user_id' => 1,
            'content' => 'Task 1 of Tester 1',
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 2,
            'user_id' => 1,
            'content' => 'Updated Task 2 of Tester 1',
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 3,
            'user_id' => 2,
            'content' => 'Task 1 of Tester 2',
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 4,
            'user_id' => 2,
            'content' => 'Task 2 of Tester 2',
            'deleted_at' => null,
        ]);
    }

    public function testHasManySyncCreatesUpdatesAndButDoesNotDeleteIfDeletingIsDeactivated()
    {
        // Arrange
        $this->createTwoUsersWithTwoTasksEach();
        $user1 = User::find(1);
        $user2 = User::find(2);

        // Act
        $user1->tasks()->sync([
            // Create
            [
                'id' => null,
                'content' => 'Tasks 3 of Tester 1',
            ],
            // Update
            [
                'id' => 2,
                'content' => 'Updated Task 2 of Tester 1',
            ],
            // Delete, because task with id=1 is missing
        ], false);

        // Assert
        $this->assertEquals(5, Task::count());
        $this->assertEquals(5, Task::withTrashed()->count());
        $this->assertDatabaseHas('tasks', [
            'content' => 'Tasks 3 of Tester 1',
            'user_id' => 1,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 1,
            'user_id' => 1,
            'content' => 'Task 1 of Tester 1',
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 2,
            'user_id' => 1,
            'content' => 'Updated Task 2 of Tester 1',
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 3,
            'user_id' => 2,
            'content' => 'Task 1 of Tester 2',
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => 4,
            'user_id' => 2,
            'content' => 'Task 2 of Tester 2',
            'deleted_at' => null,
        ]);
    }
}

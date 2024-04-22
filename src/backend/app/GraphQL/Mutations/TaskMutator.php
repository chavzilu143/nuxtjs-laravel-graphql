<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Log;

final class TaskMutator
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue
     * @param  mixed[]  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context 
     * @return mixed
     */
    public function create($rootValue, array $args, GraphQLContext $context)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            throw new \Exception("Unauthenticated.");
        }

        // Create the task
        $task = new Task($args);
        
        // Associate the task with the authenticated user
        $task->user()->associate(Auth::user());
        
        // Save the task
        $task->save();

        return $task;
    }

    /**
     * Update an existing task.
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @return \App\Models\Task
     */
    public function update($rootValue, array $args, GraphQLContext $context)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            throw new \Exception("Unauthenticated.");
        }

        // Find the task by ID
        $task = Task::findOrFail($args['id']);

        // Log user_id and Auth::id()
        Log::info('User ID: ' . $task->user_id . ', Auth ID: ' . Auth::id());

        // Ensure the authenticated user owns the task
        if ($task->user_id != Auth::id()) {
            throw new \Exception("Unauthorized.");
        }

        // Update the task attributes
        $task->fill($args);

        // Save the updated task
        $task->save();

        return $task;
    }

    /**
     * Delete an existing task.
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @return \App\Models\Task
     */
    public function delete($rootValue, array $args, GraphQLContext $context)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            throw new \Exception("Unauthenticated.");
        }

        // Find the task by ID
        $task = Task::findOrFail($args['id']);

        // Log user_id and Auth::id()
        Log::info('User ID: ' . $task->user_id . ', Auth ID: ' . Auth::id());

        // Ensure the authenticated user owns the task
        if ($task->user_id != Auth::id()) {
            throw new \Exception("Unauthorized.");
        }

        // Delete the task
        $task->delete();

        return $task;
    }
}

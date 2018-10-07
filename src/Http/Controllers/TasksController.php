<?php

namespace Studio\Novacron\Http\Controllers;

use Studio\Totem\Contracts\TaskInterface;

class TasksController
{
    /**
     * @var \Studio\Totem\Contracts\TaskInterface
     */
    private $tasks;

    public function __construct(TaskInterface $tasks)
    {
        $this->tasks = $tasks;
    }

    public function index()
    {
        return $this->tasks->findAll();
    }
}
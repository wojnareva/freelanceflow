<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::Todo => 'To Do',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Blocked => 'Blocked',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Todo => 'gray',
            self::InProgress => 'blue',
            self::Completed => 'green',
            self::Blocked => 'red',
        };
    }
}
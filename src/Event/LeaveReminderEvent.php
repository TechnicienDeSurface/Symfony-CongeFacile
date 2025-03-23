<?php 
// src/Event/LeaveReminderEvent.php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class LeaveReminderEvent extends Event {
    public const NAME = 'leave.reminder';

    protected $leave;

    public function __construct($leave) {
        $this->leave = $leave;
    }

    public function getLeave() {
        return $this->leave;
    }
}
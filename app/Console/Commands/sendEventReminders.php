<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('app:send-event-reminders')]
#[Description('Send Notifications reminders to all attendees of upcoming events')]
class sendEventReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to send event reminders...');

        $upcomingEvents = Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])
            ->get();

        $eventCount = $upcomingEvents->count();
        $eventLabel = Str::plural('event', $eventCount);

        if ($upcomingEvents->isEmpty()) {
            $this->comment('No upcoming events found requiring reminders.');
            return Command::SUCCESS;
        }
        
        $this->info("Found {$eventCount} {$eventLabel}.");
        
        // Start the progress bar
        $this->output->progressStart($eventCount);

        foreach ($upcomingEvents as $event) {
            foreach ($event->attendees as $attendee) {
                // Send the notification to the user
                $attendee->user?->notify(new EventReminderNotification($event));
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        
        $this->info("Reminder notifications sent successfully.");
        return Command::SUCCESS;
    }
}

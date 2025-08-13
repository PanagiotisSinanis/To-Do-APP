Backend API Updates – Enhancements of Time Tracking

This branch implements advanced changes to the time tracking functionality of the TimeEntryController, integrating smarter tracking logic and task integration. 

1. TimeEntryController

Smart Start/Stop: Using the current_start_time field, time tracking is more precise, and time is properly accumulated. Upon stopping a timer, the elapsed time divides the total and is added to the cumulative total. If a timer is started on a previously worked on task, it resumes the previous entry instead of spawning a new entry.

Task Integration: Time entries can be associated to tasks using task_id. Upon stopping a timer for a task, the task status is changed to ‘pending’ automatically. 

New Methods: 
To ensure safer time entry deletion, a new destroy method was added.

The entries method now fetches data more efficiently using eager loading, and thus, the method is improved.

Simplified API: the server now automatically calculates the start/end time for the timer, thus the client is freed from sending this data, making the stop method simpler.

2. New Database Migrations

To support these features, the following new migrations are created:

add_task_id_to_time_entries_table.php: relate TimeEntry records to Tasks with task_id.

add_current_start_time_to_time_entries_table.php: introduces new field to be able to track time accurately.

project_and_times_to_tasks_table.php: A migration to add necessary columns for the project.


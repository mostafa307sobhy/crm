<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property string $doc_name
 * @property string $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereDocName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment whereUpdatedAt($value)
 */
	class Attachment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $status
 * @property int $is_active
 * @property string $package_type
 * @property string|null $sub_start_date
 * @property string|null $subscription_duration
 * @property string|null $sub_end_date
 * @property string|null $sub_renew_date
 * @property string|null $quick_note
 * @property string|null $tax_number
 * @property string|null $commercial_register
 * @property string|null $critical_alert
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $assignedUsers
 * @property-read int|null $assigned_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Operation> $operations
 * @property-read int|null $operations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCommercialRegister($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCriticalAlert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePackageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereQuickNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSubEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSubRenewDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSubStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSubscriptionDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 */
	class Client extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property int $user_id
 * @property string $action_text
 * @property string|null $file_path
 * @property string|null $file_name
 * @property int $is_pinned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereActionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operation whereUserId($value)
 */
	class Operation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action_type
 * @property string $action_details
 * @property string|null $ip_address
 * @property string|null $location_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereActionDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereLocationInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemLog whereUserId($value)
 */
	class SystemLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property string $task_desc
 * @property string|null $request_date
 * @property string $priority
 * @property string $recurrence
 * @property string|null $recurrence_end_date
 * @property int $created_by
 * @property string $status
 * @property int|null $completed_by
 * @property string|null $completed_at
 * @property string $deadline
 * @property string $recurrence_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $assignedUsers
 * @property-read int|null $assigned_users_count
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\User $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCompletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRecurrence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRecurrenceEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRecurrenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRequestDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTaskDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SystemLog> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Operation> $operations
 * @property-read int|null $operations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent {}
}


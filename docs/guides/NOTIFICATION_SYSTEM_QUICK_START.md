# Notification System - Quick Start Guide 🚀

## 📦 Send a Notification (Copy-Paste Ready)

### Basic Notification
```php
use App\Models\UserNotification;

UserNotification::create([
    'user_id' => $userId,
    'type' => 'your_type',
    'title' => 'Your Title',
    'body' => 'Your message here',
]);
```

### Styled Notification (Recommended)
```php
UserNotification::create([
    'user_id' => $userId,
    'type' => 'verification_approved',
    'title' => 'Success! ✅',
    'body' => 'Your request has been approved',
    'icon' => '✅',
    'color' => 'green',
    'priority' => 'high',
    'action_url' => route('your.route'),
]);
```

## 🎨 Available Colors & Icons

### Colors
```php
'color' => 'green'   // Success states
'color' => 'red'     // Errors/rejections
'color' => 'blue'    // Info/neutral
'color' => 'yellow'  // Warnings
'color' => 'orange'  // Changes needed
'color' => 'purple'  // Special/premium
```

### Common Icons
```php
'icon' => '✅'  // Approved
'icon' => '❌'  // Rejected
'icon' => '📝'  // Changes needed
'icon' => '⏳'  // Pending
'icon' => '🎉'  // Celebration
'icon' => '💰'  // Money/rewards
'icon' => '💳'  // Payment
'icon' => '💬'  // Message
'icon' => '📅'  // Calendar/appointment
'icon' => '🔔'  // General notification
'icon' => '📢'  // Announcement
'icon' => '🏆'  // Achievement
```

## 🎯 Priority Levels

```php
'priority' => 'normal'   // Default, everyday notifications
'priority' => 'high'     // Important, action required
'priority' => 'critical' // Urgent, immediate attention
```

## 📍 Example Use Cases

### 1. Payment Success
```php
UserNotification::create([
    'user_id' => $order->user_id,
    'type' => 'payment_success',
    'title' => 'Payment Successful 💳',
    'body' => "Your payment of ৳{$order->amount} has been processed successfully.",
    'icon' => '💳',
    'color' => 'green',
    'priority' => 'high',
    'action_url' => route('orders.show', $order),
]);
```

### 2. Document Verification
```php
UserNotification::create([
    'user_id' => $document->user_id,
    'type' => 'document_verified',
    'title' => 'Document Verified ✓',
    'body' => "Your {$document->type} has been verified successfully.",
    'icon' => '✓',
    'color' => 'green',
    'priority' => 'normal',
    'action_url' => route('documents.show', $document),
]);
```

### 3. Application Status Update
```php
UserNotification::create([
    'user_id' => $application->user_id,
    'type' => 'application_approved',
    'title' => 'Application Approved! 🎉',
    'body' => "Your {$application->job_title} application has been approved.",
    'icon' => '🎉',
    'color' => 'green',
    'priority' => 'high',
    'action_url' => route('applications.show', $application),
]);
```

### 4. Message Received
```php
UserNotification::create([
    'user_id' => $recipient->id,
    'type' => 'message_received',
    'title' => "New message from {$sender->name}",
    'body' => Str::limit($message->content, 100),
    'icon' => '💬',
    'color' => 'blue',
    'priority' => 'normal',
    'action_url' => route('messages.show', $message),
]);
```

### 5. Admin Announcement
```php
// Send to multiple users
$users = User::active()->get();

foreach ($users as $user) {
    UserNotification::create([
        'user_id' => $user->id,
        'type' => 'admin_message',
        'title' => '📢 Important Announcement',
        'body' => 'Platform maintenance scheduled for tonight 10 PM - 12 AM.',
        'icon' => '📢',
        'color' => 'purple',
        'priority' => 'high',
        'action_url' => route('announcements.index'),
    ]);
}
```

## 🔍 Query Notifications

### Get Unread Notifications
```php
$unread = UserNotification::where('user_id', Auth::id())
    ->whereNull('read_at')
    ->latest()
    ->get();
```

### Get Unread Count
```php
$count = UserNotification::where('user_id', Auth::id())
    ->whereNull('read_at')
    ->count();
```

### Get Recent Notifications
```php
$recent = UserNotification::where('user_id', Auth::id())
    ->latest()
    ->limit(10)
    ->get();
```

### Mark as Read
```php
$notification->update(['read_at' => now()]);
// or
$notification->markRead();
```

### Mark All as Read
```php
UserNotification::where('user_id', Auth::id())
    ->whereNull('read_at')
    ->update(['read_at' => now()]);
```

### Delete Notification
```php
$notification->delete();
```

## 🌐 API Endpoints

### Get Dropdown Data
```
GET /notifications/dropdown
Response: { notifications: [...], unread_count: 5 }
```

### Get Unread Count
```
GET /notifications/unread-count
Response: { unread_count: 5 }
```

### Mark Single as Read
```
POST /notifications/{id}/read
```

### Mark All as Read
```
POST /notifications/read-all
```

### Delete Notification
```
DELETE /notifications/{id}
```

### Full Notifications Page
```
GET /notifications
Returns: Inertia page with paginated notifications
```

## 🎭 Frontend Usage

### Check if Component Exists
The NotificationBell component is already integrated in the header. You don't need to do anything!

### Add New Notification Type Icon
Edit `resources/js/Components/NotificationBell.vue`:

```js
const typeIcons = {
  // Existing types...
  'your_new_type': '🆕',  // Add here
}
```

## ⚡ Tips & Best Practices

1. **Always include action_url** - Makes notifications clickable
2. **Use meaningful titles** - Users see this first
3. **Keep body concise** - 1-2 sentences max
4. **Choose appropriate priority** - Don't overuse 'critical'
5. **Use emojis sparingly** - One per notification
6. **Test with real data** - Send yourself a test notification

## 🧪 Testing

### Send Test Notification
```bash
php artisan tinker
```

```php
DB::table('user_notifications')->insert([
    'user_id' => 1,
    'type' => 'test',
    'title' => 'Test Notification',
    'body' => 'This is a test',
    'icon' => '🔔',
    'color' => 'blue',
    'priority' => 'normal',
    'created_at' => now(),
    'updated_at' => now(),
]);
```

### Check Notification Count
```bash
php artisan tinker
```

```php
echo UserNotification::count();
echo UserNotification::whereNull('read_at')->count(); // Unread
```

## 🐛 Troubleshooting

### Notification not showing?
1. Check user_id matches logged-in user
2. Verify database record created
3. Check browser console for errors
4. Clear cache: `php artisan cache:clear`

### Bell icon not visible?
1. Rebuild frontend: `npm run build`
2. Check AuthenticatedLayout.vue has NotificationBell imported
3. Verify user is authenticated

### Dropdown not opening?
1. Check JavaScript console for errors
2. Verify routes are registered: `php artisan route:list | grep notification`
3. Test API endpoint: `/notifications/dropdown`

## 📚 Files to Know

```
Backend:
├── app/Models/UserNotification.php
├── app/Http/Controllers/NotificationController.php
├── database/migrations/*_user_notifications*.php
└── routes/web.php (notification routes)

Frontend:
├── resources/js/Components/NotificationBell.vue
└── resources/js/Layouts/AuthenticatedLayout.vue
```

---

**Need help? Check NOTIFICATION_SYSTEM_COMPLETE.md for full documentation.**

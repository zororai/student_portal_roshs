<?php

namespace App\Http\Controllers;

use App\SchoolNotification;
use App\NotificationRead;
use App\User;
use App\Student;
use App\Teacher;
use App\Parents;
use App\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolNotificationController extends Controller
{
    /**
     * Admin: Display all notifications
     */
    public function adminIndex()
    {
        $notifications = SchoolNotification::with(['sender', 'class', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.admin.notifications.index', compact('notifications'));
    }

    /**
     * Admin: Show create notification form
     */
    public function create()
    {
        $classes = Grade::orderBy('class_name')->get();
        $teachers = Teacher::with('user')->get();
        $students = Student::with('user', 'class')->get();
        $parents = Parents::with('user')->get();

        return view('backend.admin.notifications.create', compact('classes', 'teachers', 'students', 'parents'));
    }

    /**
     * Admin: Store new notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,teachers,students,parents,class,individual',
            'class_id' => 'required_if:recipient_type,class',
            'recipient_id' => 'required_if:recipient_type,individual',
            'priority' => 'required|in:low,normal,high,urgent'
        ]);

        $notification = SchoolNotification::create([
            'title' => $request->title,
            'message' => $request->message,
            'recipient_type' => $request->recipient_type,
            'class_id' => $request->recipient_type === 'class' ? $request->class_id : null,
            'recipient_id' => $request->recipient_type === 'individual' ? $request->recipient_id : null,
            'sent_by' => Auth::id(),
            'priority' => $request->priority
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification sent successfully!');
    }

    /**
     * Admin: View notification details
     */
    public function show($id)
    {
        $notification = SchoolNotification::with(['sender', 'class', 'recipient', 'reads.user'])
            ->findOrFail($id);

        return view('backend.admin.notifications.show', compact('notification'));
    }

    /**
     * Admin: Delete notification
     */
    public function destroy($id)
    {
        $notification = SchoolNotification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully!');
    }

    /**
     * User: View notifications inbox
     */
    public function inbox()
    {
        $user = Auth::user();
        $notifications = $this->getNotificationsForUser($user);

        return view('backend.notifications.inbox', compact('notifications'));
    }

    /**
     * User: Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = SchoolNotification::findOrFail($id);
        
        NotificationRead::updateOrCreate(
            [
                'notification_id' => $id,
                'user_id' => Auth::id()
            ],
            [
                'read_at' => now()
            ]
        );

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Get notifications for a specific user based on their role
     */
    private function getNotificationsForUser($user)
    {
        $query = SchoolNotification::with(['sender', 'class'])
            ->orderBy('created_at', 'desc');

        $notifications = $query->get()->filter(function ($notification) use ($user) {
            switch ($notification->recipient_type) {
                case 'all':
                    return true;
                
                case 'teachers':
                    return $user->hasRole('Teacher');
                
                case 'students':
                    return $user->hasRole('Student');
                
                case 'parents':
                    return $user->hasRole('Parent');
                
                case 'class':
                    if ($user->hasRole('Student')) {
                        $student = Student::where('user_id', $user->id)->first();
                        return $student && $student->class_id == $notification->class_id;
                    }
                    if ($user->hasRole('Parent')) {
                        $parent = Parents::where('user_id', $user->id)->first();
                        if ($parent) {
                            $students = Student::where('parent_id', $parent->id)->pluck('class_id')->toArray();
                            return in_array($notification->class_id, $students);
                        }
                    }
                    return false;
                
                case 'individual':
                    return $notification->recipient_id == $user->id;
                
                default:
                    return false;
            }
        });

        // Add read status to each notification
        $readNotifications = NotificationRead::where('user_id', $user->id)
            ->pluck('notification_id')
            ->toArray();

        return $notifications->map(function ($notification) use ($readNotifications) {
            $notification->is_read = in_array($notification->id, $readNotifications);
            return $notification;
        });
    }

    /**
     * Get unread notification count for current user
     */
    public static function getUnreadCount()
    {
        $user = Auth::user();
        if (!$user) return 0;

        $controller = new self();
        $notifications = $controller->getNotificationsForUser($user);
        
        return $notifications->filter(function ($n) {
            return !$n->is_read;
        })->count();
    }
}

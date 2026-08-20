<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationTemplate;
use App\Http\Requests\NotificationFormRequest;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationDispatcher;
class NotificationController extends Controller
{
    public function index(){

        $notifications = NotificationTemplate::all();
        return view('admin.notifications.all_notifications',compact('notifications'));

    }

    public function notifications(){

        return view('admin.notifications.add_notification');

    }
    public function addNotifications(NotificationFormRequest $request)
    {
        // dd($request->all());
        try {
            if (NotificationTemplate::where('title', $request->title)->exists()) {
                return back()->with('error', 'A notification with this title already exists.');
            }

            if (NotificationTemplate::where('type', $request->type)->exists()) {
                return back()->with('error', 'A notification with this type already exists.');
            }
            $notification = new NotificationTemplate();
            $notification->title = $request->title;
            $notification->type = $request->type;
            $notification->content = $request->content;
            $notification->save();

            DB::commit();
            // trigger on event
           //NotificationDispatcher::dispatch('new_notification_added', auth()->user());

            return redirect('/admin-dashboard/notifications')->with('success','Notification Added Successfully.');
        }catch(\Exception $e){

            saveLog("Error:", "NotificationController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }

    }
    public function editNotification($id){
        $notification = NotificationTemplate::findOrFail($id);

        return view('admin.notifications.add_notification',compact('notification'));
    }
    public function updateNotification(NotificationFormRequest $request, $id)
{
    try {
        $notification = NotificationTemplate::findOrFail($id);

        // Check if title already exists (excluding current)
        $existingTitle = NotificationTemplate::where('title', $request->title)
            ->where('id', '!=', $id)
            ->exists();

        if ($existingTitle) {
            return redirect()->back()->with('error', 'A notification with this title already exists.');
        }

        // Check if type already exists (excluding current)
        $existingType = NotificationTemplate::where('type', $request->type)
            ->where('id', '!=', $id)
            ->exists();

        if ($existingType) {
            return redirect()->back()->with('error', 'A notification with this type already exists.');
        }

        $notification->update(
            collect($request->validated())->only(['title', 'content'])->toArray()
        );

        return redirect('/admin-dashboard/notifications')->with('success', 'Notification updated successfully!');
    } catch (\Exception $e) {
        saveLog("Error:", "NotificationController", $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    }
}

    public function destroy($id)
    {
        try {
            $notification = NotificationTemplate::findOrFail($id);
            $notification->delete();

            return redirect('/admin-dashboard/notifications')->with('success', 'Notification deleted successfully!');
        } catch (\Exception $e) {
            saveLog("Error:", "NotificationController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
       }
    }
}

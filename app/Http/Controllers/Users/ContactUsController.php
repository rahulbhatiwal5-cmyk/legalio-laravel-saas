<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;
use App\Models\Media;
use App\Services\FileUploadService;
use App\Models\AdminContactUs;
use Exception;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Str;
use App\Services\MediaService;


class ContactUsController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService){
        $this->fileUploadService = $fileUploadService;
    }

    public function index(){
        $contact = AdminContactUs::first();
        return view('users.contact.contactUs',compact('contact'));
    }

    public function contactUsProcc(Request $request, MediaService $mediaService)
    {
        // dd($request->all());
        $request->validate([
            'reason_id' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'fileInput' => 'nullable|file|mimes:jpg,jpeg,svg,png,pdf,docx,txt|max:2048',
        ]);
        
        try {
            // Create Ticket (ticket_id will be generated in the Ticket model boot method)
            $ticket = Ticket::create([
            
                'user_id' => auth()->id(),
                'reason_id' => $request->reason_id,
                'subject' => $request->subject,
                'status' => 'open',
                'seen_status' => false,
            ]);
         
            $media = null;
            if ($request->hasFile('fileInput')) {
                $media = $mediaService->uploadMedia($request->file('fileInput'), 'ticket_attachments');
            }
          
            // dd($media);
            // Create Ticket Message
          TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'sent_by' => 'user',
                'message' => $request->message,
                'media_id' => $media ? $media->id : null,
                'seen_status' => false,
             
            ]);

            return redirect()->back()->with('ticket_success', [
                'message' => 'Thank you for reaching out to Legalio Support. Your ticket has been created and our team is already on it. Here are the details of your request.<br><br>
                              <strong>Ticket Number:</strong> ' . $ticket->ticket_id . '<br>
                              <strong>Date Submitted:</strong> ' . now()->format('Y-m-d') . '<br><br>
                              Our support team will review your request and get back to you as soon as possible. In the meantime, you can track the status of your ticket using the link below.',
                'ticket_id' => $ticket->ticket_id,
            ]);
            
            
            
        } catch (\Exception $e) {
      
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}

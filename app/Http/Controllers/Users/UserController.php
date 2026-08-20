<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\BillingAdress;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Question;
use App\Models\DocumentRightSection;
use App\Models\SaveContractQuestion;
use App\Models\ContractContent;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\SavedDataId;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\AiAssistantChat;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use CloudConvert\CloudConvert;
use CloudConvert\Models\Task;
use CloudConvert\Models\Job;
use Http;
use App\Services\ApplePageService;
use App\Services\DocxToPagesService;
use App\Services\MediaService;
use App\Models\UserCredit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use App\Models\GrantedDocument;
use App\Models\Plans;
use App\Models\FreeGrantAccess;
use Stripe\PaymentIntent;
use App\Models\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('user_dashboard.dashboard.dashboard');
    }

    public function profile()
    {
        $user = Auth::user();
        $billingAddress = BillingAdress::where('user_id', $user->id)->first();
        // dd($billingAddress);
        return view('user_dashboard.profile.profile', compact('user', 'billingAddress'));
    }

    public function userProfileUpdate(Request $request, $id)
    {
        

        $user = User::findOrFail($id);
        $billingAddress = BillingAdress::where('user_id', $user->id)->first();

        if (!$billingAddress) {
            $billingAddress = new BillingAdress();
            $billingAddress->user_id = $user->id;
        }

        $billingAddress->company = $request->company;
        $billingAddress->save();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->public_name = $request->public_name;
        $user->rfc = $request->rfc;

        $user->save();

        return redirect()
            ->back()
            ->with('success', 'Perfil actualizado correctamente.');
    }
    public function userBillingUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $billingAddress = BillingAdress::where('user_id', $user->id)->first();

        if (!$billingAddress) {
            $billingAddress = new BillingAdress();
            $billingAddress->user_id = $user->id;
        }

        $billingAddress->address = $request->address;
        $billingAddress->city = $request->city;
        $billingAddress->state = $request->state;
        $billingAddress->postal_code = $request->postal_code;
        $billingAddress->country = $request->country;
        $billingAddress->save();

        return redirect()
            ->back()
            ->with('success', 'Perfil y dirección de facturación actualizados correctamente.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $hasActiveSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        if ($hasActiveSubscription) {
            return redirect()->back()->with('error', 'Your account cannot be deleted while you have an active subscription. Please cancel your subscription first.');
        }

        $hasUnpaidOrders = Order::where('user_id', $user->id)
            ->where('status', '0')
            ->exists();

        if ($hasUnpaidOrders) {
            return redirect()->back()->with('error', 'Your account cannot be deleted while you have outstanding payments.');
        }

        if ($user->file_path && Storage::exists($user->file_path)) {
            Storage::delete($user->file_path);
        }

        $user->addresses()->delete();
        $user->orders()->delete();
        $user->delete();

        Auth::logout();
        return redirect('/')->with('success', 'Tu cuenta ha sido eliminada correctamente.');
    }

    public function uploadImage(Request $request)
    {

        try {
            if (!$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'No image uploaded!']);
            }

            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $user = Auth::user();

            if ($user->file_name && File::exists(public_path($user->file_path))) {
                File::delete(public_path($user->file_path));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Save in storage/app/public/profile_images
            $path = $image->storeAs('profile_images', $imageName, 'public');

            $user->file_name = $imageName;
            $user->directory_name = 'profile_images';
            $user->file_path = 'storage/' . $path; // Important for asset() to work
            $user->save();

            return response()->json([
                'success' => true,
                'image' => asset($user->file_path),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function profileUpdate()
    {
        return view('user_dashboard.profile.profile');
    }
    // configuration
    public function configuration()
    {
        return view('user_dashboard.configuration.configuration');
    }
    public function configurationUpdate(Request $request)
    {
        $request->validate([
            'CurrentPassword' => 'required',
            'NewPassword' => 'required|string|min:6',
            'ConfirmPassword' => 'required|same:NewPassword',
        ]);
        $user = Auth::user();
        // dd($user);
        if (Hash::check($request->CurrentPassword, $user->password)) {
            $user->update(['password' => Hash::make($request->NewPassword)]);
        }
        Auth::logout();
        return redirect()
            ->back()
            ->with('success', 'Password updated successfully');
    }

    // saved

    public function saved()
    {
        $user_id = auth()->user()->id;

        $savedData = SavedDataId::where('user_id', $user_id)->get();
        $documentIds = $savedData->pluck('document_id');
        $savedIDMap = $savedData->pluck('id', 'document_id');
        $savedDocumentIDMap = $savedData->pluck('document_id', 'id');

        $attemptedQuestions = SaveContractQuestion::whereIn('saved_id', $savedIDMap->values())->get();
        $firstQuestionsPerDocument = [];
        $lastQuestionsPerDocument = [];

        foreach ($savedIDMap as $documentId => $savedId) {
            $questions = $attemptedQuestions->where('saved_id', $savedId);
            if (!$questions->isEmpty()) {
                $firstQuestionsPerDocument[$documentId] = $questions->first();
                $lastQuestionsPerDocument[$documentId] = $questions->last();
            }
        }

        $documents = Document::whereIn('id', $documentIds)->get();

        return view('user_dashboard.saved.saved', compact('documents', 'lastQuestionsPerDocument', 'attemptedQuestions', 'documentIds', 'savedDocumentIDMap', 'firstQuestionsPerDocument'));
    }

    public function getSavedStep(Request $request)
    {
        $user_id = auth()->id();
        $document_id = $request->document_id;

        // Get saved data ID for this user + document
        $savedData = SavedDataId::where('user_id', $user_id)
            ->where('document_id', $document_id)
            ->first();

        if (!$savedData) {
            return response()->json(['step_id' => null, 'message' => 'No saved progress'], 404);
        }

        // Get all attempted questions for this saved ID
        $attemptedQuestions = SaveContractQuestion::where('saved_id', $savedData->id)
            ->orderBy('id') // assuming order matters by ID
            ->get();

        if ($attemptedQuestions->isEmpty()) {
            return response()->json(['step_id' => null, 'message' => 'No attempted questions'], 404);
        }

        // Get the last attempted question_id
        $lastQuestion = $attemptedQuestions->last();
        return response()->json([
            'step_id' => $lastQuestion->question_id,
            'message' => 'Last step found',
        ]);
    }

    public function renameSaved(Request $request, $id)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
        ]);

        $document = Document::findOrFail($id);

        $userOwns = SavedDataId::where('user_id', auth()->id())
            ->where('document_id', $id)
            ->exists();

        abort_unless($userOwns, 403);

        $document->title = $request->title;
        $document->short_description = $request->short_description;
        $document->save();

        return redirect()->back()->with('success', 'Draft renamed successfully.');
    }

    public function savedEdit()
    {
        return view('user_dashboard.saved.savedEdit');
    }

    public function invoice()
    {
        $invoices = Order::with(['transaction', 'document'])
            ->where('user_id', Auth::id())
            ->where('status', '1')
            ->paginate(5);


        // dd($invoices);
        return view('user_dashboard.invoices.invoice', compact('invoices'));
    }

    public function orderInvoiceView($id)
    {
        $order = Order::where('order_num', $id)->first();
        $userAddress = $order->user && $order->user?->addresses ? $order->user->addresses->first() : null;

        $grantedDocument = GrantedDocument::where(
            [
                ['user_id', $order->user?->id],
                ['document_id', $order->document?->id],
                ['order_id', $order?->id]
            ]
        )->first();

        if ($grantedDocument && !empty($grantedDocument->granted_document_id)) {
            $decoded = json_decode($grantedDocument->granted_document_id, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $grantedDocIds = $decoded;
            } else {
                $grantedDocIds = [$grantedDocument->granted_document_id];
            }
        } else {
            $grantedDocIds = [];
        }

        $months = $grantedDocument?->free_interval;
        $free_interval = str_replace('months', '', $months);
        $plan_id = $grantedDocument?->plan_id;
        $plans = Plans::all();
        $free_grant = web_setting('free_grant_expiration')->value;

        $totalOrder = Order::where([['user_id', $order->user_id], ['status', 1]])->count();
        $totalRevenue = Order::where([['user_id', $order->user_id], ['status', 1]])
            ->with(['transactions' => function ($query) {
                $query->where('status', 'succeeded');
            }])->get()
            ->sum(function ($sum) {
                return $sum->transactions->total_amount ?? 0;
            });

        $averageOrderValue = $totalOrder > 0 ? $totalRevenue / $totalOrder : 0;
        Carbon::setLocale('en');
        $date = Carbon::parse($order->transactions->created_at ?? '');
        $formattedDate = $date->translatedFormat('F d, Y');
        $documents = Document::where('published', '1')->get();
        $payment = $order->transactions;
        $freeGrant = FreeGrantAccess::where('order_id', $order?->id)->where('is_granted', 1)->with('grantedDocument', 'freeSubscription')->first();

        if ($payment?->type == "stripe") {
            try {
                $paymentIntent = PaymentIntent::retrieve($order->transactions->payment_intent);
                $paymentMethodId = $paymentIntent->payment_method;
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
                $cardDetails = [
                    'brand' => $paymentMethod->card->brand,
                    'last4' => $paymentMethod->card->last4,
                    'exp_month' => $paymentMethod->card->exp_month,
                    'exp_year' => $paymentMethod->card->exp_year,
                ];
                $subscription = null;
                if ($order->order_type == 'subscription') {
                    $subscription = Subscription::where('stripe_subscription_id', $order?->stripe_subscription_id)->with('plan')->first();
                }
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return view('user_dashboard.invoices.orderDetail', compact('id', 'order', 'totalOrder', 'totalRevenue', 'averageOrderValue', 'payment', 'paymentIntent', 'cardDetails', 'formattedDate', 'userAddress', 'documents', 'grantedDocIds', 'subscription', 'plans', 'free_interval', 'plan_id', 'free_grant', 'freeGrant'));
        } elseif ($payment?->type == "paypal") {
            $paymentMethod = $order->paypal_order_id;
            return view('user_dashboard.invoices.orderDetail', compact('id', 'order', 'totalOrder', 'totalRevenue', 'averageOrderValue', 'payment', 'paymentMethod', 'formattedDate', 'userAddress', 'documents', 'grantedDocIds', 'freeGrant', 'plans', 'free_interval', 'plan_id'));
        } else {
            dd("Something went wrong...");
        }
    }

    public function print($orderId)
    {
        $order = Order::with(['user.addresses', 'document'])->findOrFail($orderId);

        $address = optional($order->user->addresses->first());
        $document = $order->document;

        $price = $order->amount ?? 0;

        $data = [
            'customer_name'    => $order->user->first_name,
            'customer_address' => $address->address ?? 'N/A',
            'customer_email'   => $address->user->email ?? 'N/A',
            'invoice_id'       => $order->order_num,
            'invoice_date'     => $order->created_at->format('d M, Y'),

            'items' => [[
                'image'       => $order->document->document_image,
                'description' => $document->title ?? 'Document',
                'price'       => $price,
                'quantity'    => 1,
            ]],

            'subtotal'       => $price,
            'processing_fee' => $order->processing_fee ?? 0,
            'tax'            => $order->tax ?? 0,
        ];

        $data['total'] = $data['subtotal'] + $data['processing_fee'] + $data['tax'];

        return view('user_dashboard.invoices.printInvoice', $data);
    }

    public function downloadInvoice($id, DocxToPagesService $DocxToPagesService)
    {
        $view = 'admin.orders.Invoice_pdf_template';
        $order = Order::with(['user.addresses', 'document'])->findOrFail($id);

        $address = optional($order->user->addresses->first());
        $document = $order->document;
        $price = $order->amount ?? 0;

        $originalPath = $order->document->document_image;
        Log::info("Original image path: {$originalPath}");

        $cleanPath = $originalPath;

        if (preg_match('|^https?://|', $cleanPath)) {
            $parsedUrl = parse_url($cleanPath);
            if (isset($parsedUrl['path'])) {
                $cleanPath = ltrim($parsedUrl['path'], '/');

                if (preg_match('|(assets/img/.*)|', $cleanPath, $matches)) {
                    $cleanPath = $matches[1];
                }
            }
        }

        Log::info("Cleaned path: {$cleanPath}");

        // Now get the absolute file path for this image
        $absolutePath = public_path($cleanPath);
        Log::info("Absolute path: {$absolutePath}");

        // Let's try different approaches for the SVG
        $svgApproaches = [
            'base64' => null,
            'data_uri' => null,
            'public_url' => null,
            'direct_embed' => null
        ];

        if (file_exists($absolutePath) && strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) === 'svg') {
            // 1. Base64 approach
            $svgContent = file_get_contents($absolutePath);
            $svgApproaches['base64'] = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

            // 2. Data URI with URL encoding
            $svgApproaches['data_uri'] = 'data:image/svg+xml;charset=utf-8,' . urlencode($svgContent);

            // 3. Public URL - this will be an absolute URL to the image
            $svgApproaches['public_url'] = url($cleanPath);

            // 4. Direct embed (with some cleaning)
            $cleanedSvg = preg_replace('/<\?xml.*?\?>/', '', $svgContent);
            // Make sure SVG has explicit dimensions
            if (!preg_match('/width=/', $cleanedSvg)) {
                $cleanedSvg = preg_replace('/<svg/', '<svg width="60" height="60"', $cleanedSvg);
            }
            $svgApproaches['direct_embed'] = $cleanedSvg;
        } else {
            Log::warning("SVG file not found: {$absolutePath}");
        }

        $data = [
            'customer_name'    => $order->user->first_name,
            'customer_address' => $address->address ?? 'N/A',
            'customer_email'   => $address->user->email ?? 'N/A',
            'invoice_id'       => $order->order_num,
            'invoice_date'     => $order->created_at->format('d M, Y'),

            'items' => [[
                'image_approaches' => $svgApproaches,
                'image_path' => $absolutePath,
                'description' => $document->title ?? 'Document',
                'price'       => $price,
                'quantity'    => 1,
            ]],

            'subtotal'       => $price,
            'processing_fee' => $order->processing_fee ?? 0,
            'tax'            => $order->tax ?? 0,
        ];

        $data['total'] = $data['subtotal'] + $data['processing_fee'] + $data['tax'];

        return $DocxToPagesService->generatePDF($view, $data, "invoice-order-{$order->id}.pdf", false);
    }

    public function purchase()
    {
        $user_id = auth()->user()->id;

        if ($user_id) {
            $orders = Order::where([['user_id', $user_id], ['status', 1]])
                ->with('document')
                ->get();

            $user = User::find($user_id);
            $has_credits = $user->hasCredits();
            $orderIds = $orders->pluck('id');

            $contractContents = ContractContent::where('user_id', $user_id)
                ->whereIn('order_id', $orderIds)
                ->get()
                ->keyBy('order_id');

            $userCredits = UserCredit::where('user_id', $user_id)
                ->get()
                ->groupBy('document_id');

            $copiedContract = ContractContent::where('user_id', $user_id)
                ->whereIn('order_id', $orderIds)
                // ->where('type','copied')
                ->get()
                ->keyBy('order_id');
            // dd($userCredits);

            $purchased_documents = $orders->map(function ($order) use ($contractContents, $userCredits) {
                $contractContent = $contractContents->get($order->id);
                $edit_count = $contractContent ? $contractContent->edit_count : 0;

                $credits = $userCredits->get($order->document_id, collect());
                $creditsLeft = $credits->first()?->balance ?? 0;

                return [
                    'order_date' => $order->created_at->format('d/m/Y'),
                    'document' => $order->document,
                    'order_id' => $order->id,
                    'has_subscription' => $order->hasSubscription(),
                    'edit_count' => $edit_count,
                    'credits' => $creditsLeft,
                ];
            });

            $copied_documents = ContractContent::where('user_id', $user_id)
                // ->where('type', 'copied')
                ->with('document') // loads the related Document
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('document_id');

            // dd($copied_documents);

        }

        return view('user_dashboard.purchased.purchased', compact('purchased_documents', 'has_credits', 'copied_documents'));
    }


    public function review()
    {
        $user = User::with('addresses')->find(auth()->id());
        $reviews = Review::with('document')->where('user_id', $user->id)->where('status', 1)->get();
        $documents = Document::unreviewedPaidDocuments()->get();
        $documentCount = $documents->count();

        // dd($documentCount);

        return view('user_dashboard.review.review', compact('user', 'reviews', 'documents', 'documentCount'));
    }



    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'city' => 'required|string',
            'description' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        try {
            if (auth()->user()->is_admin == 1) {
                $type = "custom";
            } else {
                $type = "user";
            }

            $review = new Review();
            $review->document_id = $request->document_id;
            $review->user_id = auth()->user()->id;
            $review->city = $request->city;
            $review->description = $request->description;
            $review->rating = $request->rating;
            $review->first_name = $request->first_name;
            $review->last_name = $request->last_name;
            $review->date = Carbon::parse($request->date)->format('Y-m-d');
            $review->type = $type;
            $review->status = 0;
            $review->save();

            return redirect()->route('user.review')->with('success', 'Review submitted successfully.');
        } catch (\Exception $e) {
            saveLog("Error:", "HomeController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateReview(Request $request)
    {
        // dd($request->rating);
        $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'city' => 'required|string|max:255',
            'description' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        try {
            $review = Review::findOrFail($request->review_id);

            $review->city = $request->city;
            $review->description = $request->description;
            $review->rating = $request->rating ?? $review->rating;
            // dd($review->rating , $request->rating);
            $review->save();

            return redirect()->back()->with('success', 'Review updated successfully.');
        } catch (\Exception $e) {
            saveLog("Review Update Error:", "ReviewController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function destroyReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }

    public function createPDF()
    {
        return view('users.contracts_pdf.create_pdf');
    }

    //  public function generatePDF(Request $request)
    // {
    //     $user_id = auth()->user()->id;
    //     $document_id = $request->id;
    //     $order_id = $request->order_id ?? null;

    //     $contractContent = null;

    //     if ($order_id) {
    //         $contractContent = ContractContent::where([
    //             ['user_id', $user_id],
    //             ['document_id', $document_id],
    //             ['order_id', $order_id],
    //             ['status', 1]
    //         ])->latest()->first();

    //         if (!$contractContent) {
    //             $contractContent = ContractContent::where([
    //                 ['user_id', $user_id],
    //                 ['document_id', $document_id],
    //                 ['status', 1]
    //             ])->latest()->first();
    //         }
    //     } else {
    //         $contractContent = ContractContent::where([
    //             ['user_id', $user_id],
    //             ['document_id', $document_id],
    //             ['status', 1]
    //         ])->latest()->first();
    //     }

    //     if (!$contractContent) {
    //         $sections = DocumentRightSection::where('document_id', $document_id)
    //             ->orderBy('order_id', 'asc')
    //             ->get();

    //         if ($sections->isEmpty()) {
    //             return response()->json([
    //                 'code' => 404,
    //                 'message' => 'Contract content not found for document ID: ' . $document_id,
    //             ], 404);
    //         }
            
    //         $htmlContent = '';
    //         foreach ($sections as $sec) {
    //             $content = $sec->content;
    //             if ($sec->type === 'content_heading') {
    //                 $htmlContent .=  $content ;
    //             } elseif ($sec->type === 'signature_field') {
    //                 $htmlContent .= '<div style="margin-top:40px;border-top:1px solid #000;width:300px;">' . $content . '</div>';
    //             } else {
    //                 $htmlContent .= $content;
    //             }
    //         }

    //         $data = ['document_content' => $htmlContent];
    //         $pdf = PDF::loadView('users.contracts_pdf.contract_pdf', $data);
    //         return $pdf->download('document.pdf');
    //     }

    //     $data = ['document_content' => $contractContent->html];
    //     $pdf = PDF::loadView('users.contracts_pdf.contract_pdf', $data);
    //     return $pdf->download('document.pdf');
    // }


    public function generatePDF(Request $request)
        {
            $user_id     = auth()->user()->id;
            $document_id = $request->id;
            $order_id    = $request->order_id ?? null;

            $existingContract = ContractContent::where('user_id', $user_id)
                ->where('document_id', $document_id)
                ->when($order_id, fn($q) => $q->where('order_id', $order_id))
                ->latest()
                ->first();

            if (!$existingContract) {
                $existingContract = ContractContent::where('user_id', $user_id)
                    ->where('document_id', $document_id)
                    ->latest()
                    ->first();
            }

            $answers = [];
            if ($existingContract) {
                preg_match_all(
                    '/<span[^>]*qidtarget-(\d+)[^>]*>(.*?)<\/span>/is',
                    $existingContract->html,
                    $m
                );
                foreach ($m[1] as $i => $qid) {
                    $val = strip_tags($m[2][$i]);
                    if ($val !== '' && $val !== '_______' && $val !== '__________') {
                        $answers[$qid] = $val;
                    }
                }
            }

            $savedData = SavedDataId::where('user_id', $user_id)
                ->where('document_id', $document_id)
                ->first();

            if ($savedData) {
                $dbAnswers = SaveContractQuestion::where('saved_id', $savedData->id)
                    ->pluck('answer', 'question_id')
                    ->toArray();
                foreach ($dbAnswers as $qid => $val) {
                    if (!isset($answers[$qid]) && $val !== null && $val !== '') {
                        $answers[$qid] = $val;
                    }
                }
            }

            if (empty($answers)) {
                $questionIds = Question::where('document_id', $document_id)->pluck('id')->toArray();
                if (!empty($questionIds)) {
                    $query = SaveContractQuestion::whereIn('question_id', $questionIds);
                    if ($savedData) {
                        $query->where('saved_id', $savedData->id);
                    }
                    $dbAnswers = $query->pluck('answer', 'question_id')->toArray();
                    foreach ($dbAnswers as $qid => $val) {
                        if ($val !== null && $val !== '') {
                            $answers[$qid] = $val;
                        }
                    }
                }
            }

            $cleanHtmlForPdf = function (string $html) use ($answers): string {
                $html = preg_replace('/<div[^>]*class="[^"]*target-box[^"]*"[^>]*>.*?<\/div>\s*<\/div>/is', '', $html);
                $html = preg_replace('/<div[^>]*class="[^"]*target-box[^"]*"[^>]*>.*?<\/div>/is', '', $html);
                $html = preg_replace('/Por favor,\s*completa\s*los\s*datos\s*en\s*el\s*formulario\s*ubicado\s*a\s*la\s*izquierda\.?/is', '', $html);
                $html = preg_replace('/<span[^>]*class="[^"]*pop-span[^"]*"[^>]*>.*?<\/span>/is', '', $html);
                $html = preg_replace('/<img[^>]*>/is', '', $html);
                $html = preg_replace('/<[^>]+class="[^"]*\bhide\b[^"]*"[^>]*>.*?<\/[^>]+>/is', '', $html);
                $html = preg_replace('/<div[^>]*class="[^"]*secure_blur_sec[^"]*"[^>]*>.*?<\/div>/is', '', $html);
                $html = preg_replace('/<div[^>]*class="[^"]*secure_content[^"]*"[^>]*>.*?<\/div>/is', '', $html);
                $html = preg_replace('/<span[^>]*class="[^"]*text-hover[^"]*"[^>]*>.*?<\/span>/is', '', $html);
                $html = preg_replace_callback(
                    '/<span[^>]*(?:answered_spns|qidtarget-\d+)[^>]*>(.*?)<\/span>/is',
                    function ($m) {
                        $val = strip_tags($m[1]);
                        return ($val !== '' && $val !== '_______' && $val !== '__________') ? $val : '________';
                    },
                    $html
                );

                // Replace remaining raw {N} / {QIDN} placeholders
                $html = preg_replace_callback(
                    '/\{(?:[W_]*QID)?(\d+)(?:[^}]*)?\}/i',
                    function ($matches) use ($answers) {
                        $value = $answers[$matches[1]] ?? null;
                        return ($value !== null && $value !== '') ? $value : '________';
                    },
                    $html
                );
                $html = preg_replace('/\{[^}]+\}/', '', $html);

                return $html;
            };

            if ($existingContract) {
                $htmlContent = $cleanHtmlForPdf($existingContract->html);

                $pdf = PDF::loadView('users.contracts_pdf.contract_pdf', [
                    'document_content' => $htmlContent,
                ]);
                return $pdf->download('document.pdf');
            }

            $sections = DocumentRightSection::where('document_id', $document_id)
                ->orderBy('order_id', 'asc')
                ->get();

            if ($sections->isNotEmpty()) {
                $htmlContent = '';

                $replacePlaceholders = function (string $content) use ($answers): string {
                    return preg_replace_callback(
                        '/\{(?:[W_]*QID)?(\d+)(?:[^}]*)?\}/i',
                        function ($matches) use ($answers) {
                            $value = $answers[$matches[1]] ?? null;
                            return ($value !== null && $value !== '') ? $value : '________';
                        },
                        $content
                    );
                };

                foreach ($sections as $sec) {
                    if ($sec->secure_blur_content) {
                        continue;
                    }

                    $content = $replacePlaceholders($sec->content ?? '');
                    if ($sec->type === 'content_heading') {
                        $htmlContent .= $content;
                    } elseif ($sec->type === 'signature_field') {
                        $htmlContent .= '<div style="margin-top:40px;border-top:1px solid #000;width:300px;">'
                            . $content . '</div>';
                    } else {
                        $htmlContent .= $content;
                    }
                }

                ContractContent::updateOrCreate(
                    [
                        'user_id'     => $user_id,
                        'document_id' => $document_id,
                        'order_id'    => $order_id,
                    ],
                    [
                        'html'   => $htmlContent,
                        'status' => 1,
                    ]
                );

                $pdf = PDF::loadView('users.contracts_pdf.contract_pdf', [
                    'document_content' => $htmlContent,
                ]);
                return $pdf->download('document.pdf');
            }

            return response()->json([
                'code'    => 404,
                'message' => 'Contract content not found for document ID: ' . $document_id,
            ], 404);
        }
        
    public function adminGeneratePDF(Request $request, $id)
    {
        //  $request->all();

        $order = Order::find($id);
        $user_id = $order->user_id;

        $document_id = $order->document_id;

        if ($id) {
            $contractContent = ContractContent::where([
                ['user_id', $user_id],
                ['document_id', $document_id],
                ['order_id', $id],
                ['status', 1]
            ])->first();
        } else {
            $contractContent = ContractContent::where([
                ['user_id', $user_id],
                ['document_id', $document_id],
                ['status', 1]
            ])->latest()->first();
        }

        if ($contractContent) {
            $data = ['document_content' => $contractContent->html];
            $pdf = PDF::loadView('users.contracts_pdf.contract_pdf', $data);
            return $pdf->download('document.pdf');
        }
    }

    // Download DOCX
    public function generateDOCX(Request $request, $id)
    {
        if (!class_exists(\ZipArchive::class)) {
            return back()->with('error', 'DOCX export is not supported on this server.');
        }
        $order = Order::find($id);
        $user_id = $order->user_id;

        $document_id = $order->document_id;
        $contractContent = ContractContent::where([['user_id', $user_id], ['document_id', $document_id]])->first();
        // dd($contractContent);

        if ($contractContent) {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $section->addText(strip_tags($contractContent->html));

            // Define the file path
            $fileName = "invoice_{$document_id}.docx";
            $filePath = storage_path("app/{$fileName}");

            // Save the DOCX file correctly
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($filePath);



            // Return the DOCX file as a response
            return response()
                ->download($filePath)
                ->deleteFileAfterSend(true);
        }

        // Handle the case if no content is found
        return redirect()
            ->back()
            ->with('error', 'Invoice not found.');
    }


   
    public function aiAssistant()
    {
        $userId = auth()->id();
        $conversations = AiAssistantChat::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('user_dashboard.aiassistant.ai_assistant', compact('conversations'));
    }



    

    public function convertToPages($id)
    {
        $docxPath = app(DocxToPagesService::class)->generateDOCX($id);
        // return $docxPath;


        if (!$docxPath || !file_exists($docxPath)) {
            return redirect()->back()->with('error', 'Failed to generate DOCX.');
        }

        // Step 2: Convert to PAGES via API
        $pagesPath = app(DocxToPagesService::class)->convert($docxPath);

        $downloadName = 'invoice_' . $id . '.pages';

        $response = response()->download($pagesPath, $downloadName)->deleteFileAfterSend(true);

        // Manually create the cookie and attach it to the response headers
        $cookie = Cookie::make('downloadComplete', 'true', 1);

        // Attach cookie header to the response
        $response->headers->setCookie($cookie);

        return $response;
    }

    public function support()
    {
        $tickets = Ticket::where('user_id', auth()->user()->id)->whereHas('messages')->with('messages')->get();
        // dd($tickets);
        return view('user_dashboard.support.support', compact('tickets'));
    }

    public function supportView($id)
    {
        $ticket = Ticket::where('user_id', auth()->user()->id)->where('ticket_id', $id)->with('messages')->first();
        // dd($tickets);
        return view('user_dashboard.support.supportView', compact('ticket'));
    }

    public function userReply(Request $request, $ticketId, MediaService $mediaService)
    {
        // dd($request->all());
        $request->validate([
            'message' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048'
        ]);

        $ticket = Ticket::findOrFail($ticketId);
        $mediaId = null;

        if ($request->hasFile('media')) {


            $media = $mediaService->uploadMedia($request->file('media'), 'tickets');

            $mediaId = $media->id;
        }

        $message = new TicketMessage();
        $message->ticket_id = $ticket->id;
        $message->user_id = auth()->id();
        $message->sent_by = 'user';
        $message->message = $request->input('message');
        $message->media_id = $mediaId;
        $message->seen_status = false;
        $message->save();

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

 public function saveBillingInfo(Request $request)
{
    return response()->json([
        'success' => true,
        'redirect_url' => route('home')
    ]);
}
}

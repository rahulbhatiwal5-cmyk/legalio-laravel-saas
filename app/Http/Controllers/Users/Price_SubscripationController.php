<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\ContractContent;
use App\Models\BillingAdress;
use App\Models\Plans;
use App\Models\PlanIncludedDocument;
use App\Models\UserPlan;
use App\Models\UserCredit;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use App\Models\Discount;
use Exception;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    
    
    

}
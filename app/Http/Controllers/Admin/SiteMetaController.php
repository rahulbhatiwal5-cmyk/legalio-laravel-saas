<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\HowItWork;
use App\Models\Work;
use App\Models\TermsAndCondition;
use App\Models\AdminContactUs;
use App\Models\LoginRegister;
use App\Models\PrepareContract;
use App\Models\PrepareContractWork;
use App\Models\Media;
use App\Services\FileUploadService;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\HomeContent;
use App\Models\HomeCategories;
use App\Models\HomeTemplate;
use App\Models\Setting;
use App\Models\PricesContent;
use App\Models\HelpCenter;
use App\Models\HelpYou;
use Exception;
use File;
use Hash;
use Illuminate\Support\Facades\Cache;

class SiteMetaController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService){
        $this->fileUploadService = $fileUploadService;
    }

    public function howItWorks(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'short_description',
            'second_banner_img',
            'second_banner_heading',
            'second_banner_sub_heading',
            'button_label',
            'button_link',
            'meta_title',
            'meta_description',
        ];

        $results = HowItWork::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'bg_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image_id' => $results['banner_image']->id ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'short_description' => $results['short_description']->value ?? null,
            'second_banner_id' => $results['second_banner_img']->id ?? null,
            'second_banner_img' => str_replace('public/', '', $results['second_banner_img']->file_path ?? null),
            'second_banner_heading' => $results['second_banner_heading']->value ?? null,
            'second_banner_sub_heading' => $results['second_banner_sub_heading']->value ?? null,
            'button_label' => $results['button_label']->value ?? null,
            'button_link' => $results['button_link']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
        ];

        $works = Work::with('media')->get();

        return view('admin.site_meta.howItWorks.add_how_it_work', compact('works', 'data'));

    }

    public function addHowItWorks(Request $request){
        try{
            if($request->hasFile('background_image')){
                $file = $request->file('background_image');
                $directory = "public/how_it_work";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $how_it_works = HowItWork::where('key','background_image')->first();
                $how_it_works->value = $filename;
                $how_it_works->file_path = $filepath;
                $how_it_works->update();
            }

            if($request->hasFile('banner_image')){
                $file = $request->file('banner_image');

                $directory = "public/how_it_work";
                $filename = generateFileName($file);

                $filepath = $file->storeAs($directory, $filename);

                $how_it_works = HowItWork::where('key','banner_image')->first();

                $how_it_works->value = $filename;

                $how_it_works->file_path = $filepath;

                $how_it_works->update();
            }

            if($request->hasFile('second_banner_img')){
                $file = $request->file('second_banner_img');
                $directory = "public/how_it_work";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $how_it_works = HowItWork::where('key','second_banner_img')->first();
                $how_it_works->value = $filename;
                $how_it_works->file_path = $filepath;
                $how_it_works->update();
            }

            if($request->has('work_heading') || $request->has('work_description')){
                foreach($request->work_heading as $key=>$value){
                    $works = Work::find($key);
                    $works->heading = $value;
                    $works->update();
                }

                foreach($request->work_description as $id=>$description){
                    $works = Work::find($id);
                    $works->description	= $description;
                    $works->update();
                }
            }

            if($request->hasFile('new_work_image')  && $request->has('new_work_heading') && $request->has('new_work_description')){
                for($i=0; $i<count($request->file('new_work_image')); $i++){
                    $file = $request->file('new_work_image')[$i];
                    $new_work_heading = $request->new_work_heading[$i];
                    $new_work_description = $request->new_work_description[$i];

                    $directory = "public/how_it_work";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();

                    if(isset($fileuploadData) && $fileuploadData->status == '200'){
                        $works = new Work;
                        $works->media_id = $fileuploadData->id;
                        $works->heading = $new_work_heading;
                        $works->description = $new_work_description;
                        $works->save();

                    }elseif($fileuploadData->status == '400') {
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            $fields = [
                'title' => 'title',
                'banner_title' => 'banner_title',
                'banner_description' => 'banner_description',
                'main_heading' => 'main_heading',
                'short_description' => 'short_description',
                'second_banner_heading' => 'banner_heading',
                'second_banner_sub_heading' => 'sub_heading',
                'button_label' => 'button_label',
                'button_link' => 'button_link',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
            ];

            foreach($fields as $key=>$input){
                if($request->has($input)) {
                    $How_it_work = HowItWork::where('key', $key)->first();
                    if($How_it_work){
                        $How_it_work->value = $request->$input;
                        $How_it_work->update();
                    }else{
                        $How_it_work = new HowItWork;
                        $How_it_work->key = $key;
                        $How_it_work->value = $request->$input;
                        $How_it_work->save();
                    }
                }
            }

            if($request->bg_img_id != null){
                $how_it_work = HowItWork::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($how_it_work->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $how_it_work->value = null;
                $how_it_work->file_path = null;
                $how_it_work->update();
            }

            if($request->baner_image_id != null){
                $how_it_work = HowItWork::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($how_it_work->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $how_it_work->value = null;
                $how_it_work->file_path = null;
                $how_it_work->update();
            }

            if($request->second_id != null){
                $how_it_work = HowItWork::where('id',$request->second_id)->first();
                $file_path = getFilePath($how_it_work->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $how_it_work->value = null;
                $how_it_work->file_path = null;
                $how_it_work->update();
            }

            if($request->work_img_id != null){
                $deleteIds = explode(',', $request->work_img_id);
                foreach($deleteIds as $id){
                    $work = Work::where('id',$id)->with('media')->first();
                    if($work->media){
                        $image_path = getFilePath($work->media->file_path);
                        if(File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id',$work->media_id)->delete();

                        $work->media_id = null;
                        $work->update();
                    }
                }
            }

            if($request->delete_work_ids != null){
                $deleteIds = explode(',', $request->delete_work_ids);
                foreach($deleteIds as $id){
                    $work = Work::where('id',$id)->with('media')->first();
                    if($work->media){
                        $image_path = getFilePath($work->media->file_path);
                        if(File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id',$work->media_id)->delete();

                        $work->delete();
                    }
                }
            }

            return redirect()->back()->with('success', 'Data successfully updated');

        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateWorkImage(Request $request){
        if($request->work_id != null){
            $id = $request->work_id;
            if($request->hasFile('image')) {
                $file = $request->file('image');
                $directory = "public/how_it_work";
                $fileupload = $this->fileUploadService->upload($file, $directory);
                $fileuploadData = $fileupload->getData();
                $works = Work::find($id);

                if(isset($fileuploadData) && $fileuploadData->status == '200'){
                    $works->media_id = $fileuploadData->id;
                    $works->update();

                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'success',
                    ];

                }elseif($fileuploadData->status == '400'){
                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'fail',
                    ];
                }

                return response()->json($response);

            }else{
                return response()->json([
                    'code' => '400',
                    'status' => 'fail',
                    'message' => 'No file uploaded',
                ], 400);
            }
        }
    }

    public function termsConditions(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'meta_title',
            'meta_description',
        ];

        $results = TermsAndCondition::whereIn('key',$keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'bg_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image_id' => $results['banner_image']->id ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
        ];
        $termsAndCondition = TermsAndCondition::where('key','terms_and_condition')->get();

        return view('admin.site_meta.terms_and_conditions.terms_and_conditions',compact('termsAndCondition','data'));
    }

    public function addTermsAndCondition(Request $request){
        try{
            if($request->hasFile('background_image')){
                $file = $request->file('background_image');

                $filename = generateFileName($file);

                $directory = 'public/terms_and_conditions';
                $path = $file->storeAs($directory, $filename);

                $termsCondition = TermsAndCondition::where('key','background_image')->first();

                $termsCondition->value = $filename;

                $termsCondition->file_path = $path;

                $termsCondition->update();
            }

            if($request->hasFile('banner_image')){
                $file = $request->file('banner_image');
                $filename = generateFileName($file);
                $directory = 'public/terms_and_conditions';
                $path = $file->storeAs($directory, $filename);

                $termsCondition = TermsAndCondition::where('key','banner_image')->first();
                $termsCondition->value = $filename;
                $termsCondition->file_path = $path;
                $termsCondition->update();
            }

            if($request->has('terms')){
                foreach($request->terms as $index=>$value){
                    $termsCondition = TermsAndCondition::find($index);
                    $termsCondition->terms = $value;
                    $termsCondition->update();
                }

                foreach($request->condition as $key=>$val){
                    $termsCondition = TermsAndCondition::find($key);
                    $termsCondition->condition = $val;
                    $termsCondition->update();
                }
            }

            if($request->has('new_terms')){
                for($i=0; $i<count($request->new_terms); $i++){
                    $new_terms = $request->new_terms[$i];

                    $terms_and_condition = new TermsAndCondition;
                    $terms_and_condition->key = 'terms_and_condition';
                    $terms_and_condition->type = 'terms';
                    $terms_and_condition->terms = $new_terms;

                    if($request->new_condition != null){
                        $new_condition = $request->new_condition[$i];
                    }
                    $terms_and_condition->condition = $new_condition;
                    $terms_and_condition->save();
                }
            }

            $keys = [
                'title' => 'title',
                'main_heading' => 'main_heading',
                'banner_title' => 'banner_title',
                'banner_description' => 'banner_description',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description'
            ];

            foreach($keys as $key=>$input){
                if($request->has($input)){
                    $terms_and_condition = TermsAndCondition::where('key', $key)->first();
                    if($terms_and_condition){
                        $terms_and_condition->value = $request->$input;
                        $terms_and_condition->update();
                    }else{
                        $terms_and_condition = new TermsAndCondition;
                        $terms_and_condition->key = $key;
                        $terms_and_condition->value = $request->$input;
                        $terms_and_condition->save();
                    }
                }
            }

            if($request->bg_img_id != null){
                $terms_and_condition = TermsAndCondition::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($terms_and_condition->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $terms_and_condition->value = null;
                $terms_and_condition->file_path = null;
                $terms_and_condition->update();
            }

            if($request->baner_image_id != null){
                $terms_and_condition = TermsAndCondition::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($terms_and_condition->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $terms_and_condition->value = null;
                $terms_and_condition->file_path = null;
                $terms_and_condition->update();
            }

            if($request->remove_ids != null){
                $removeIds = explode(',', $request->remove_ids);
                $termsCondition = TermsAndCondition::whereIn('id',$removeIds)->delete();
            }

            return redirect()->back()->with('success','Terms and Conditions successfully saved');
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function contactUs(){
        $contact = AdminContactUs::first();

        return view('admin.site_meta.contactUs.contact_us',compact('contact'));
    }

    public function addContactProcc(Request $request){
        try{
            if($request->id != null){
                $contact = AdminContactUs::find($request->id);
                $status = 'updated';
            }else{
                $contact = new AdminContactUs;
                $status = 'saved';
            }

            if($request->hasFile('background_image')){
                $file = $request->file('background_image');
                $filename = generateFileName($file);
                $directory = 'public/contact';
                $path = $file->storeAs($directory, $filename);
                $contact->background_image = $filename;
                $contact->background_image_path = $path;
            }

            if($request->hasFile('banner_image')){
                $file = $request->file('banner_image');
                $filename = generateFileName($file);
                $directory = 'public/contact';
                $path = $file->storeAs($directory, $filename);
                $contact->banner_image = $filename;
                $contact->banner_image_path = $path;
            }

            $contact->title = $request->title;
            $contact->meta_title = $request->meta_title;
            $contact->meta_description = $request->meta_description;
            $contact->banner_title = $request->banner_title;
            $contact->banner_description = $request->banner_description;
            $contact->main_title = $request->main_title;
            $contact->save();

            if($request->bg_img_id != null){
                $contact = AdminContactUs::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($contact->background_image_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $contact->background_image = null;
                $contact->background_image_path = null;
                $contact->update();
            }

            if($request->baner_image_id != null){
                $contact = AdminContactUs::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($contact->banner_image_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $contact->banner_image = null;
                $contact->banner_image_path = null;
                $contact->update();
            }

            if($status == 'updated'){
                return redirect()->back()->with('success','Data Successfully updated');
            }elseif($status == 'saved'){
                return redirect()->back()->with('success','Data Successfully saved');
            }
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function login(){
        $login = LoginRegister::where('key','login')->first();
        return view('admin.site_meta.login.login',compact('login'));
    }

    public function addLogin(Request $request){
        try{
            if($request->id != null){
                $login = LoginRegister::find($request->id);
                $status = 'updated';
            }else{
                $login = new LoginRegister;
                $status = 'saved';
            }
            $login->key = 'login';
            $login->title = $request->title;
            $login->meta_title = $request->meta_title;
            $login->meta_description = $request->meta_description;
            $login->main_heading = $request->main_heading;
            $login->main_sub_heading = $request->main_sub_heading;

            if($request->hasFile('background_image')){
                $file = $request->file('background_image');
                $directory = "public/site_images";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $login->background_image = $filename;
                $login->file_path = $filepath;
            }

            $login->save();

            if($request->bg_img_id != null){
                $login = LoginRegister::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($login->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $login->background_image = null;
                $login->file_path = null;
                $login->update();
            }

            if($status == 'updated'){
                return redirect()->back()->with('success','Data Successfully updated');
            }elseif($status == 'saved'){
                return redirect()->back()->with('success','Data Successfully saved');
            }

        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function register(){
        $register = LoginRegister::where('key','register')->first();
        return view('admin.site_meta.register.register',compact('register'));
    }

    public function addRegister(Request $request){
        try{
            if($request->id != null){
                $register = LoginRegister::find($request->id);
                $status = 'updated';
            }else{
                $register = new LoginRegister;
                $status = 'saved';
            }
            $register->key = 'register';
            $register->title = $request->title;
            $register->meta_title = $request->meta_title;
            $register->meta_description = $request->meta_description;
            $register->main_heading = $request->main_heading;

            if($request->hasFile('background_image')){
                $file = $request->file('background_image');
                $directory = "public/site_images";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $register->background_image = $filename;
                $register->file_path = $filepath;
            }

            $register->save();

            if($request->bg_img_id != null){
                $register = LoginRegister::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($login->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $register->background_image = null;
                $register->file_path = null;
                $register->update();
            }

            if($status == 'updated'){
                return redirect()->back()->with('success','Data Successfully updated');
            }elseif($status == 'saved'){
                return redirect()->back()->with('success','Data Successfully saved');
            }

        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function prepareContract(){
        $keys = [
            'title',
            'fb_link',
            'twitter_link',
            'pinterest_link',
            'short_description',
            'description',
            'page_sub_heading',
            'work_main_heading',
            'economical_main_heading',
            'economical_description',
            'button_text',
            'button_link'
        ];

        $results = PrepareContract::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'fb_link' => $results['fb_link']->value ?? null,
            'twitter_link' => $results['twitter_link']->value ?? null,
            'pinterest_link' => $results['pinterest_link']->value ?? null,
            'short_description' => $results['short_description']->value ?? null,
            'description' => $results['description']->value ?? null,
            'page_sub_heading' => $results['page_sub_heading']->value ?? null,
            'work_main_heading' => $results['work_main_heading']->value ?? null,
            'economical_main_heading' => $results['economical_main_heading']->value ?? null,
            'economical_description' => $results['economical_description']->value ?? null,
            'button_text' => $results['button_text']->value ?? null,
            'button_link' => $results['button_link']->value ?? null,
        ];

        $image = PrepareContract::where('key','image')->whereNotNull('media_id')->with('media')->first();
        $work_sec =  PrepareContract::where('key','prepare_work')->with('contract_work','media')->get();

        return view('admin.site_meta.prepare_contract.prepare_contract',compact('data','image','work_sec'));
    }

    public function prepareContractprocc(Request $request){
        try{
            if($request->image_id != null){
                $prepare_contract = PrepareContract::where('id',$request->image_id)->with('media')->first();
                if($prepare_contract->media){
                    $image_path = storage_path('/app/'.$prepare_contract->media->file_path);
                    if (File::exists($image_path)) {
                        unlink($image_path);
                    }
                    $prepare_contract->media_id = null;
                    $prepare_contract->update();
                }
            }

            if($request->hasFile('image')){
                $image = $request->file('image');
                $fileupload = $this->fileUploadService->upload($image, 'public');

                $fileuploadData = $fileupload->getData();

                if(isset($fileuploadData) && $fileuploadData->status == '200'){
                    $prepare_contract = PrepareContract::where('key','image')->first();
                    if($prepare_contract){
                        $prepare_contract->media_id = $fileuploadData->id;
                        $prepare_contract->update();
                    }else{
                        $prepare_contract = new PrepareContract;
                        $prepare_contract->key = 'image';
                        $prepare_contract->type = 'file';
                        $prepare_contract->media_id = $fileuploadData->id;
                        $prepare_contract->save();
                    }

                }elseif($fileuploadData->status == '400') {
                    return redirect()->back()->with('error', $fileuploadData->error);
                }
            }

            if($request->has('work_header')){
                foreach($request->work_header as $key=>$value){
                    $prepare_contract_work = PrepareContractWork::find($key);
                    $prepare_contract_work->header = $value;
                    $prepare_contract_work->update();
                }

                foreach($request->work_short_description as $id=>$description){
                    $prepare_contract_work = PrepareContractWork::find($key);
                    $prepare_contract_work->description = $description;
                    $prepare_contract_work->update();
                }
            }

            if($request->hasFile('new_work_icon')){
                $icon = $request->file('new_work_icon');
                for($i=0; $i<count($icon); $i++){
                    $file = $icon[$i];

                    if($request->has('new_work_header')){
                        $work_header = $request->new_work_header[$i];
                    }

                    if($request->has('new_work_short_description')){
                        $work_short_description = $request->new_work_short_description[$i];
                    }

                    $fileupload = $this->fileUploadService->upload($file, 'public');

                    $fileuploadData = $fileupload->getData();

                    if(isset($fileuploadData) && $fileuploadData->status == '200'){
                        $prepare_contract = new PrepareContract;
                        $prepare_contract->key = 'prepare_work';
                        $prepare_contract->type = 'work';

                        $prepare_contract_work = new PrepareContractWork;
                        $prepare_contract_work->media_id = $fileuploadData->id;
                        $prepare_contract_work->header = $work_header;
                        $prepare_contract_work->description = $work_short_description;
                        $prepare_contract_work->save();

                        $prepare_contract->media_id = $fileuploadData->id;
                        $prepare_contract->prepare_work_id = $prepare_contract_work->id;
                        $prepare_contract->save();

                    }elseif($fileuploadData->status == '400') {
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            $fields = [
                'title' => 'title',
                'fb_link ' => 'fb_link',
                'twitter_link' => 'twitter_link',
                'pinterest_link' => 'pinterest_link',
                'short_description' => 'short_description',
                'description' => 'description',
                'page_sub_heading' => 'page_sub_heading',
                'work_main_heading' => 'work_main_heading',
                'economical_main_heading' => 'economical_main_heading',
                'economical_description' => 'economical_description',
                'button_text' => 'button_text',
                'button_link' => 'button_link',
            ];

            foreach($fields as $key=>$input){
                if($request->has($input)) {
                    $prepare_contract = PrepareContract::where('key', $key)->first();
                    if ($prepare_contract) {
                        $prepare_contract->value = $request->$input;
                        $prepare_contract->update();
                    }
                }
            }

            if($request->work_sec_ids != null){
                $deleteIds = explode(',', $request->work_sec_ids);
                foreach($deleteIds as $id){
                    $prepare_contract_work = PrepareContract::where('prepare_work_id',$id)->with('media')->first();
                    if($prepare_contract_work->media){
                        $image_path = storage_path('/app/'.$prepare_contract_work->media->file_path);
                        if (File::exists($image_path)) {
                            unlink($image_path);
                        }
                        $prepare_contract_work->delete();
                    }
                    PrepareContractWork::where('id', $id)->delete();
                }
            }

            return redirect()->back()->with('success', 'Data successfully updated');
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function homepage(){
        $document_category = DocumentCategory::where('is_deleted',0)->get();

        $keys = [
            'title',
            'background_image',
            'banner_image',
            'banner_title',
            'banner_description',
            'banner_placeholder',
            'button_name',
            'most_popular_title',
            'most_popular_btn_text',
            'popular',
            'bottom_heading',
            'bottom_subheading',
            'bottom_button_label',
            'bottom_button_link',
            'bottom_banner_image',
            'category_title',
            'category_btn_arrow_img',
            'join_us_text',
            'reviews_heading',
            'reviews_sub_heading',
            'review_left_arrow',
            'review_right_arrow',
            'meta_title',
            'meta_description',
            'home_text_google',
            'home_text_facebook',
            'home_text_email',
            'home_text_register'
        ];

        $results = HomeContent::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'background_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_image_id' =>  $results['banner_image']->id ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_placeholder' => $results['banner_placeholder']->value ?? null,
            'button_name' => $results['button_name']->value ?? null,
            'most_popular_title' => $results['most_popular_title']->value ?? null,
            'most_popular_btn_text' => $results['most_popular_btn_text']->value ?? null,
            'popular' => $results['popular']->value ?? null,
            'bottom_heading' => $results['bottom_heading']->value ?? null,
            'bottom_subheading' => $results['bottom_subheading']->value ?? null,
            'bottom_button_label' => $results['bottom_button_label']->value ?? null,
            'bottom_button_link' => $results['bottom_button_link']->value ?? null,
            'bottom_banner_id' => $results['bottom_banner_image']->id ?? null,
            'bottom_banner_image' => str_replace('public/', '', $results['bottom_banner_image']->file_path ?? null),
            'category_title' => $results['category_title']->value ?? null,
            'join_us_text' => $results['join_us_text']->value ?? null,
            'reviews_heading' => $results['reviews_heading']->value ?? null,
            'reviews_sub_heading' => $results['reviews_sub_heading']->value ?? null,
            'left_arrow_id' => $results['review_left_arrow']->id ?? null,
            'review_left_arrow' => str_replace('public/', '', $results['review_left_arrow']->file_path ?? null),
            'right_arrow_id' => $results['review_right_arrow']->id ?? null,
            'review_right_arrow' => str_replace('public/', '', $results['review_right_arrow']->file_path ?? null),
            'home_text_email' => $results['home_text_email']->value ?? null,
            'home_text_facebook' => $results['home_text_facebook']->value ?? null,
            'home_text_google' => $results['home_text_google']->value ?? null,
            'home_text_register' => $results['home_text_register']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
        ];

        $home = HomeCategories::with('media')->get();

        return view('admin.site_meta.home.home_content',compact('document_category','data','home'));
    }

    public function addHomeContent(Request $request){
        try{
            if($request->hasFile('background_image')){
                $home_content = HomeContent::where('key','background_image')->first();

                $background_image = $request->file('background_image');

                $directory = "public/home_images";
                $filename = generateFileName($background_image);
                $filepath = $background_image->storeAs($directory, $filename);

                $home_content->value = $filename;

                $home_content->file_path = $filepath;

                $home_content->update();
            }

            if($request->hasFile('banner_image')){
                $home_content = HomeContent::where('key','banner_image')->first();

                $banner_image = $request->file('banner_image');
                $directory = "public/home_images";
                $filename = generateFileName($banner_image);
                $filepath = $banner_image->storeAs($directory, $filename);

                $home_content->value = $filename;
                $home_content->file_path = $filepath;
                $home_content->update();
            }

            if($request->has('popular_documents')){
                $popular_documents = $request->popular_documents;

                $home_content = HomeContent::where('key','popular')->first();
                if(isset($home_content) && $home_content != null){
                    $home_content->value = json_encode($popular_documents);
                    $home_content->update();
                }else{
                    $home_content = new HomeContent;
                    $home_content->key = 'popular';
                    $home_content->value = json_encode($popular_documents);
                    $home_content->save();
                }
            }

            if($request->hasFile('bottom_banner_img')){
                $home_content = HomeContent::where('key','bottom_banner_image')->first();

                $file = $request->file('bottom_banner_img');
                $directory = "public/home_images";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $home_content->value = $filename;
                $home_content->file_path = $filepath;
                $home_content->update();
            }

            if($request->has('review_left_arrow')){
                $home_content = HomeContent::where('key','review_left_arrow')->first();

                $file = $request->file('review_left_arrow');
                $directory = "public/home_images";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $home_content->value = $filename;
                $home_content->file_path = $filepath;
                $home_content->update();
            }

            if($request->has('review_right_arrow')){
                $home_content = HomeContent::where('key','review_right_arrow')->first();

                $file = $request->file('review_right_arrow');
                $directory = "public/home_images";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $home_content->value = $filename;
                $home_content->file_path = $filepath;
                $home_content->update();
            }

            // if($request->hasFile('new_cat_img')){
            //     $image = $request->file('new_cat_img');

            //     for ($i = 0; $i < count($image); $i++) {
            //         $file = $image[$i];

            //         $cat_heading = $request->has('new_cat_heading') ? $request->new_cat_heading[$i] : null;
            //         $category = $request->has('new_category') ? $request->new_category[$i] : null;
            //         $category_description = $request->has('new_category_description') ? $request->new_category_description[$i] : null;
            //         $category_btn_text = $request->has('new_category_btn_text') ? $request->new_category_btn_text[$i] : null;
            //         $category_btn_link = $request->has('new_category_btn_link') ? $request->new_category_btn_link[$i] : null;

            //         $home_category = HomeCategories::create([
            //             'category_id' => $category,
            //             'heading' => $cat_heading,
            //             'category_description' => $category_description,
            //             'btn_text' => $category_btn_text,
            //             'btn_link' => $category_btn_link,
            //             'media_id' => null
            //         ]);

            //         $directory = "public/home_categories";
            //         $fileupload = $this->fileUploadService->upload($file, $directory);
            //         $fileuploadData = $fileupload->getData();

            //         if (isset($fileuploadData) && $fileuploadData->status == '200') {
            //             $home_category->media_id = $fileuploadData->id;
            //             $home_category->save();
            //         } elseif ($fileuploadData->status == '400') {
            //             return redirect()->back()->with('error', $fileuploadData->error);
            //         }
            //     }
            // }

            if($request->has('new_cat_heading')){
                $total = count($request->new_cat_heading);
            
                for ($i = 0; $i < $total; $i++) {
            
                    $file = $request->file('new_cat_img')[$i] ?? null;
            
                    $cat_heading = $request->new_cat_heading[$i] ?? null;
                    $category = $request->new_category[$i] ?? null;
                    $category_description = $request->new_category_description[$i] ?? null;
                    $category_btn_text = $request->new_category_btn_text[$i] ?? null;
                    $category_btn_link = $request->new_category_btn_link[$i] ?? null;
            
                    // ✅ skip empty rows
                    if(!$cat_heading && !$category) {
                        continue;
                    }
            
                    $home_category = HomeCategories::create([
                        'category_id' => $category,
                        'heading' => $cat_heading,
                        'category_description' => $category_description,
                        'btn_text' => $category_btn_text,
                        'btn_link' => $category_btn_link,
                        'media_id' => null
                    ]);
            
                    // upload image only if exists
                    if($file){
                        $directory = "public/home_categories";
                        $fileupload = $this->fileUploadService->upload($file, $directory);
                        $fileuploadData = $fileupload->getData();
            
                        if (isset($fileuploadData) && $fileuploadData->status == '200') {
                            $home_category->media_id = $fileuploadData->id;
                            $home_category->save();
                        }
                    }
                }
            }

            // if($request->has('cat_heading')){
            //     foreach($request->cat_heading as $key=>$val){
            //         $home_category = HomeCategories::find($key);
            //         $home_category->heading = $val;
            //         $home_category->update();
            //     }

            $deletedIds = !empty($request->category_sec) 
                ? explode(',', $request->category_sec) 
                : [];

            if($request->has('cat_heading')){
                foreach($request->cat_heading as $key=>$val){
                    if(in_array($key, $deletedIds)) continue;

                    $home_category = HomeCategories::find($key);
                    if($home_category){
                        $home_category->heading = $val;
                        $home_category->update();
                    }
                }
            

                foreach($request->category_description as $index=>$value){
                    $home_category = HomeCategories::find($index);
                    $home_category->category_description = $value;
                    $home_category->update();
                }

                foreach($request->category_btn_text as $idx=>$vlu){
                    $home_category = HomeCategories::find($idx);
                    $home_category->btn_text = $vlu;
                    $home_category->update();
                }
            }

            if($request->category != null){
                foreach($request->category as $id=>$vl){
                    $home_category = HomeCategories::find($id);
                    $home_category->category_id = $vl;
                    $home_category->update();
                }
            }

            if($request->category_btn_link != null){
                foreach($request->category_btn_link as $c_idx=>$c_vlu){
                    $home_category = HomeCategories::find($c_idx);
                    $home_category->btn_link= $c_vlu;
                    $home_category->update();
                }
            }

            $fields = [
                'title' => 'title',
                'banner_title' => 'banner_title',
                'button_name' => 'button_name',
                'banner_description' => 'banner_description',
                'banner_placeholder' => 'banner_placeholder',
                'bottom_button_link' => 'bottom_button_link',
                'most_popular_title' => 'main_title',
                'most_popular_btn_text' => 'most_popular_btn_text',
                'bottom_heading' => 'bottom_heading',
                'bottom_subheading' => 'bottom_sub_heading',
                'bottom_button_label' => 'bottom_button_label',
                'bottom_button_link' => 'bottom_button_link',
                'category_title' => 'category_main_title',
                'join_us_text' => 'join_us_text',
                'reviews_heading' => 'reviews_heading',
                'reviews_sub_heading' => 'reviews_sub_heading',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'home_text_google' => 'home_text_google' ,
                'home_text_facebook' =>'home_text_facebook' ,
                'home_text_email' =>'home_text_email' ,
                'home_text_register' => 'home_text_register',
            ];

            foreach($fields as $key=>$input){
                if($request->has($input)){
                    $home_content = HomeContent::where('key', $key)->first();
                    if($home_content){
                        $home_content->value = $request->$input;
                        $home_content->update();
                    }else{
                        $home_content = new HomeContent;
                        $home_content->key = $key;
                        $home_content->value = $request->$input;
                        $home_content->save();
                    }
                }
            }

            if($request->bg_image_id != null){
                $home_content = HomeContent::where('id',$request->bg_image_id)->first();
                $file_path = getFilePath($home_content->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $home_content->value = null;
                $home_content->file_path = null;
                $home_content->save();
            }

            if($request->baner_image_id != null){
                $home_content = HomeContent::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($home_content->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $home_content->value = null;
                $home_content->file_path = null;
                $home_content->save();
            }

            if($request->btom_banner_id != null){
                $home_content = HomeContent::where('id',$request->btom_banner_id)->first();
                $file_path = getFilePath($home_content->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $home_content->value = null;
                $home_content->file_path = null;
                $home_content->save();
            }

            if($request->rght_id != null){
                $home_content = HomeContent::where('id',$request->rght_id)->first();
                $file_path = getFilePath($home_content->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $home_content->value = null;
                $home_content->file_path = null;
                $home_content->save();
            }

            if($request->lft_id != null){
                $home_content = HomeContent::where('id',$request->lft_id)->first();
                $file_path = getFilePath($home_content->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $home_content->value = null;
                $home_content->file_path = null;
                $home_content->save();
            }

            if($request->category_img_id != null){
                $deleteIds = explode(',', $request->category_img_id);
                foreach($deleteIds as $id){
                    $home_category = HomeCategories::where('id',$id)->with('media')->first();
                    if($home_category->media){
                        $image_path = getFilePath($home_category->media->file_path);
                        if (File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                rmdir($directory_path);
                            }
                        }
                        $home_category->media_id = null;
                        $home_category->save();
                    }
                    Media::where('id',$home_category->media_id)->delete();
                }
            }

            // if($request->category_sec != null){
            //     $deleteIds = explode(',', $request->category_sec);
            //     foreach($deleteIds as $id){
            //         $home_category = HomeCategories::where('id',$id)->with('media')->first();
            //         if($home_category->media){
            //             $image_path = getFilePath($home_category->media->file_path);
            //             if (File::exists($image_path)) {
            //                 $directory_path = dirname($image_path);
            //                 unlink($image_path);
            //                 if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
            //                     rmdir($directory_path);
            //                 }
            //             }
            //             Media::where('id',$home_category->media_id)->delete();
            //             $home_category->delete();
            //         }

            //     }
            // }

            //  DELETE categories FIRST
            if(!empty($request->category_sec)) {
                $deleteIds = explode(',', $request->category_sec);

                foreach($deleteIds as $id){
                    $home_category = HomeCategories::where('id',$id)->with('media')->first();

                    if($home_category){

                        // delete image if exists
                        if($home_category->media){
                            $image_path = getFilePath($home_category->media->file_path);

                            if (File::exists($image_path)) {
                                $directory_path = dirname($image_path);
                                unlink($image_path);

                                if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                    rmdir($directory_path);
                                }
                            }

                            Media::where('id',$home_category->media_id)->delete();
                        }

                        // delete category
                        $home_category->delete();
                    }
                }
            }

            return redirect()->back()->with('success', 'Data successfully saved.');

        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateCategoryImage(Request $request){
        if($request->category_id != null){
            $id = $request->category_id;
            if($request->hasFile('cat_image')) {
                $file = $request->file('cat_image');
                $directory = "public/home_categories";
                $fileupload = $this->fileUploadService->upload($file, $directory);
                $fileuploadData = $fileupload->getData();
                $home_category = HomeCategories::find($id);

                if(isset($fileuploadData) && $fileuploadData->status == '200'){
                    $home_category->media_id = $fileuploadData->id;
                    $home_category->update();

                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'success',
                    ];

                }elseif($fileuploadData->status == '400') {
                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'fail',
                    ];
                }

                return response()->json($response);

            }else{
                return response()->json([
                    'code' => '400',
                    'status' => 'fail',
                    'message' => 'No file uploaded',
                ], 400);
            }
        }
    }


    public function getEncryptedKey($key, $value){
        if($value != null){
            if($key === 'stripe_secret_key'){
                return substr($value, 0, 4) . str_repeat('*', strlen($value) - 10) . substr($value, -4);
            }elseif($key === 'STRIPE_WEBHOOK_SECRET'){
                return substr($value, 0, 3) . str_repeat('*', strlen($value) - 10) . substr($value, -3);
            }elseif($key === 'stripe_public_key'){
                return substr($value, 0, 3) . str_repeat('*', strlen($value) - 10) . substr($value, -3);
            }elseif($key === 'paypal_secret_key'){
                return substr($value, 0, 3) . str_repeat('*', strlen($value) - 10) . substr($value, -3);
            }elseif($key === 'paypal_client_id'){
                return substr($value, 0, 3) . str_repeat('*', strlen($value) - 10) . substr($value, -3);
            }
            return $value;
        }
    }

    // public function getEncryptedKey($key, $encryptedValue) {
    //     if ($encryptedValue !== null) {
    //         try {
    //             $value = Crypt::decryptString($encryptedValue); // Decrypt the stored value

    //             $start = 0;
    //             $end = 0;

    //             // Custom masking logic per key
    //             if ($key === 'stripe_secret_key') {
    //                 $start = 4; $end = 4;
    //             } elseif ($key === 'STRIPE_WEBHOOK_SECRET') {
    //                 $start = 3; $end = 3;
    //             } elseif ($key === 'stripe_public_key') {
    //                 $start = 3; $end = 3;
    //             } elseif ($key === 'paypal_secret_key') {
    //                 $start = 3; $end = 3;
    //             } elseif ($key === 'paypal_client_id') {
    //                 $start = 3; $end = 3;
    //             }

    //             if (strlen($value) > ($start + $end)) {
    //                 return substr($value, 0, $start) . str_repeat('*', strlen($value) - $start - $end) . substr($value, -$end);
    //             } else {
    //                 // Value too short to mask normally
    //                 return str_repeat('*', strlen($value));
    //             }

    //         } catch (\Exception $e) {
    //             return str_repeat('*', 10); // fallback if decryption fails
    //         }
    //     }

    //     return null;
    // }

    public function configuration(Request $request){
        $data = Setting::where('type','review')->get();

        return view('admin.reviews.configuration',compact('data'));
    }

    public function updateConfiguration(Request $request){
        $settings = Setting::where('type',$request->type)->get();

        foreach ($settings as $setting) {
            $key = $setting->key;

            if ($request->has($key)) {
                $setting->value = $request->input($key);
                $setting->save();
            }
        }

        return redirect()->back()->with('success', 'Settings successfully updated.');

    }


    public function webSetting(Request $request ){

        $data = Setting::query();
        if ($request->filled('type')) {
            $data->where('type', $request->input('type'));
        }else {
            $data->where('type', 'config');

        }

        $data = $data->get();

        foreach ($data as $key => $setting) {
            if(in_array($setting->key, ['stripe_secret_key', 'STRIPE_WEBHOOK_SECRET', 'stripe_public_key', 'paypal_secret_key', 'paypal_client_id'])) {
                $setting->value= $this->getEncryptedKey($setting->key, $setting->value);
                // print_r($setting->value);
            }
            elseif (in_array($setting->key, ['header_logo', 'footer_logo', 'favicon', 'user_default_image'])) {
                $setting->value = isset($setting->file_path) ? str_replace('public/', '', $setting->file_path) : null;
            }

        }

        return view('admin.site_meta.web_setting.web_setting',compact('data'));
    }

    public function addWebsetting(Request $request)
    {
        try {
            $keys = array_keys($request->except('_token')); // Get all keys from the request except `_token`
            $settings = Setting::whereIn('key', $keys)->get()->keyBy('key'); // Fetch only required settings

            foreach ($keys as $key) {
                if ($request->hasFile($key)) {
                    $image = $request->file($key);
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $dir_name = 'assets/static';
                    $image->move(public_path($dir_name), $imageName);
                    $settings[$key]?->update(['value' => "$dir_name/$imageName"]);
                } else {
                    $settings[$key]?->update(['value' => $request->input($key)]);
                }
            }

            return redirect()->back()->with('success', 'Settings successfully updated.');
        } catch (Exception $e) {
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateWebsetting(Request $request){
        try {
            $type = $request->input('type', 'config');
            $settings = Setting::where('type', $type)->get();

            // Keys that should NOT be updated
            $protectedKeys = ['stripe_secret_key', 'STRIPE_WEBHOOK_SECRET', 'stripe_public_key', 'paypal_client_id','paypal_mode'];

            foreach ($settings as $setting) {
                $key = $setting->key;

                // Skip protected keys
                if (in_array($key, $protectedKeys)) {
                    continue;
                }

                // if ($request->hasFile($key)) {
                //     $image = $request->file($key);
                //     $imageName = time() . '.' . $image->getClientOriginalExtension();
                //     $dir_name = 'assets/static';
                //     $image->move(public_path($dir_name), $imageName);
                //     $setting->value = "$dir_name/$imageName";
                // } else {
                //     $setting->value = $request->input($key);
                // }

                if ($request->hasFile($key)) {
                    $image = $request->file($key);
                    $imageName = time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                    $dir_name = 'assets/static';
                    $image->move(public_path($dir_name), $imageName);
                    $setting->value = "$dir_name/$imageName";

                }elseif ($request->has($key) && $request->input($key) !== null) {
                    $setting->value = $request->input($key);
                }

                $setting->save();

                // Cache::forget('site_date_format');
            }

            return redirect()->back()->with('success', 'Settings successfully updated.');
        } catch (\Exception $e) {
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function prices(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'document_heading',
            'description_heading',
            'price_heading',
            'btn_text',
            'meta_title',
            'meta_description',
            'faq_heading',
            'faq_description',
            'subscription_title',
            'subscription_heading',
            'recommended_text',
            'subscription_description',
            'monthly_text',
            'yearly_text',
            'ahorra_text',
            'subscription_note',
            'per_month_text',
            'per_year_text',
            'subscription_btn_text',
            'one_time_heading',
            'one_time_description',
            'one_time_price_note',
            'one_time_btn_text',
        ];

        $results = PricesContent::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'bg_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image_id' => $results['banner_image']->id ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'document_heading' => $results['document_heading']->value ?? null,
            'description_heading' => $results['description_heading']->value ?? null,
            'price_heading' => $results['price_heading']->value ?? null,
            'btn_text' => $results['btn_text']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
            'faq_heading' => $results['faq_heading']->value ?? null,
            'faq_description' => $results['faq_description']->value ?? null,
            'subscription_title' => $results['subscription_title']->value ?? null,
            'subscription_heading' => $results['subscription_heading']->value ?? null,
            'recommended_text' => $results['recommended_text']->value ?? null,
            'subscription_description' => $results['subscription_description']->value ?? null,
            'monthly_text' => $results['monthly_text']->value ?? null,
            'yearly_text' => $results['yearly_text']->value ?? null,
            'ahorra_text' => $results['ahorra_text']->value ?? null,
            'subscription_note' => $results['subscription_note']->value ?? null,
            'per_month_text' => $results['per_month_text']->value ?? null,
            'per_year_text' => $results['per_year_text']->value ?? null,
            'subscription_btn_text' => $results['subscription_btn_text']->value ?? null,
            'one_time_heading' => $results['one_time_heading']->value ?? null,
            'one_time_description' => $results['one_time_description']->value ?? null,
            'one_time_price_note' => $results['one_time_price_note']->value ?? null,
            'one_time_btn_text' => $results['one_time_btn_text']->value ?? null,
        ];

        $document = Document::where('published',1)->get();
        $document_price_description = PricesContent::where('key', 'price_content')->get();
        $price_faq = PricesContent::where('key', 'faq')->get();
        // $document_price_description = Document::where('key', 'price_content')->get();

        return view('admin.site_meta.prices.price',compact('data','document','document_price_description','price_faq'));
    }

    public function addPriceContent(Request $request){
        // return $request->all();
        try{
            if($request->hasFile('background_image')){
                $background_image = $request->file('background_image');
                $directory = 'public/prices';
                $filename = generateFileName($background_image);
                $path = $background_image->storeAs($directory, $filename);

                $price_content = PricesContent::where('key','background_image')->first();
                $price_content->value = $filename;
                $price_content->file_path = $path;
                $price_content->update();
            }

            if($request->hasFile('banner_image')){
                $banner_image = $request->file('banner_image');
                $directory = 'public/prices';
                $filename = generateFileName($banner_image);
                $path = $banner_image->storeAs($directory, $filename);

                $price_content = PricesContent::where('key','banner_image')->first();
                $price_content->value = $filename;
                $price_content->file_path = $path;
                $price_content->update();
            }

            if($request->has('document')){
                $document = $request->document;
                $button_text = $request->button_text;
                $price = $request->price;
                $description = $request->description;

                foreach($document as $key => $document){
                    $price_content = PricesContent::find($key);
                    $price_content->document = $document;
                    $price_content->update();
                }

                foreach($button_text as $index => $button){
                    $price_content = PricesContent::find($index);
                    $price_content->button_text = $button;
                    $price_content->update();
                }

                foreach($price as $idx => $price){
                    $price_content = PricesContent::find($idx);
                    $price_content->price = $price;
                    $price_content->update();
                }

                foreach($description as $keyy => $description){
                    $price_content = PricesContent::find($keyy);
                    $price_content->description = $description;
                    $price_content->update();
                }
            }

            if($request->has('new_document')){
                for($i=0; $i<count($request->new_document); $i++){
                    $document = $request->new_document[$i];

                    if($request->has('new_button_text')){
                        $button_text = $request->new_button_text[$i];
                    }

                    if($request->has('new_price')){
                        $price = $request->new_price[$i];
                    }

                    if($request->has('new_description')){
                        $description = $request->new_description[$i];
                    }

                    $price_content = new PricesContent;
                    $price_content->key = 'price_content';
                    $price_content->document = $document;
                    $price_content->button_text = $button_text;
                    $price_content->price = $price;
                    $price_content->description = $description;
                    $price_content->save();
                }
            }

            if($request->has('first_question') && $request->has('first_answer')){
                $question = $request->first_question;
                $answer = $request->first_answer;

                $price_content = new PricesContent();
                $price_content->key = 'faq';
                $price_content->question = $question;
                $price_content->answer = $answer;
                $price_content->save();
            }

            if($request->has('question')){
                foreach($request->question as $key=>$val){
                    $price_content = PricesContent::find($key);
                    $price_content->question = $val;
                    $price_content->update();
                }
            }
           
            if($request->has('answer')){
                foreach($request->answer as $key=>$value){
                    $price_content = PricesContent::find($key);
                    $price_content->answer = $value;
                    $price_content->update();
                }
            }

            if($request->has('new_question') && $request->has('new_answer')){
                for($i=0; $i<count($request->new_question); $i++){
                    $question = $request->new_question[$i];
                    $answer = $request->new_answer[$i];

                    $price_content = new PricesContent;
                    $price_content->key = 'faq';
                    $price_content->question = $question;
                    $price_content->answer = $answer;
                    $price_content->save();
                }
            }

            $keys = [
                'title' => 'title',
                'banner_title' => 'banner_title',
                'banner_description' => 'banner_description',
                'document_heading' => 'document_heading',
                'description_heading' => 'description_heading',
                'price_heading' => 'price_heading',
                'btn_text'=>'button_text',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
                'faq_heading' => 'faq_heading',
                'faq_description' => 'faq_description',
                'subscription_title' => 'subscription_title',
                'subscription_heading' => 'subscription_heading',
                'recommended_text' => 'recommended_text',
                'subscription_description' => 'subscription_description',
                'monthly_text' => 'monthly_text',
                'yearly_text' => 'yearly_text',
                'ahorra_text' => 'ahorra_text',
                'subscription_note' => 'subscription_note',
                'per_month_text' => 'per_month_text',
                'per_year_text' => 'per_year_text',
                'subscription_btn_text' => 'subscription_btn_text',
                'one_time_heading' => 'one_time_heading',
                'one_time_description' => 'one_time_description',
                'one_time_price_note' => 'one_time_price_note',
                'one_time_btn_text' => 'one_time_btn_text',
            ];

            foreach($keys as $key=>$input){
                if($request->has($input)){
                    $price_content = PricesContent::where('key', $key)->first();
                    if($price_content){
                        $price_content->value = $request->$input;
                        $price_content->update();
                    }else{
                        $price_content = new PricesContent;
                        $price_content->key = $key;
                        $price_content->value = $request->$input;
                        $price_content->save();
                    }
                }
            }

            if($request->bg_img_id != null){
                $price_content = PricesContent::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($price_content->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $price_content->value = null;
                $price_content->file_path = null;
                $price_content->update();
            }

            if($request->baner_image_id != null){
                $price_content = PricesContent::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($price_content->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $price_content->value = null;
                $price_content->file_path = null;
                $price_content->save();
            }

            if($request->delete_content_ids != null){
                $deleteIds = explode(',', $request->delete_content_ids);
                foreach($deleteIds as $id){
                    $price_content = PricesContent::find($id);
                    $price_content->delete();
                }
            }

            if($request->faq_id != null){
                $faqIds = explode(',', $request->faq_id);
                foreach($faqIds as $id){
                    $price_content = PricesContent::find($id);
                    $price_content->delete();
                }
            }

            return redirect()->back()->with('success', 'Data successfully saved.');
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function helpCenter(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_placeholder',
            'banner_image',
            'main_title',
            'sub_title',
            'faq_heading',
            'faq_description',
            'bottom_banner_image',
            'banner_heading',
            'banner_description',
            'button_text',
            'meta_title',
            'meta_description',
        ];

        $results = HelpCenter::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'background_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_placeholder' => $results['banner_placeholder']->value ?? null,
            'banner_image_id' =>  $results['banner_image']->id ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_title' => $results['main_title']->value ?? null,
            'sub_title' => $results['sub_title']->value ?? null,
            'faq_heading' => $results['faq_heading']->value ?? null,
            'faq_description' => $results['faq_description']->value ?? null,
            'bottom_image_id' =>  $results['bottom_banner_image']->id ?? null,
            'bottom_banner_image' =>  str_replace('public/', '', $results['bottom_banner_image']->file_path ?? null),
            'banner_heading' => $results['banner_heading']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'button_text' => $results['button_text']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
        ];

        $faqs = HelpCenter::where('key','faq')->get();
        $help_you = HelpYou::with('media')->get();
        return view('admin.site_meta.support.support',compact('data','faqs','help_you'));
    }

    public function helpProcc(Request $request){
        try{
            if($request->hasFile('background_image')){
                $help_center = HelpCenter::where('key','background_image')->first();
                $background_image = $request->file('background_image');

                $directory = "public/help_center";
                $filename = generateFileName($background_image);
                $filepath = $background_image->storeAs($directory, $filename);

                $help_center->value = $filename;
                $help_center->file_path = $filepath;
                $help_center->update();
            }

            if($request->hasFile('banner_image')){
                $help_center = HelpCenter::where('key','banner_image')->first();

                $banner_image = $request->file('banner_image');

                $directory = "public/help_center";
                $filename = generateFileName($banner_image);
                $filepath = $banner_image->storeAs($directory, $filename);

                $help_center->value = $filename;
                $help_center->file_path = $filepath;
                $help_center->update();
            }

            if($request->hasFile('bottom_banner_image')){
                $help_center = HelpCenter::where('key','bottom_banner_image')->first();
                $file = $request->file('bottom_banner_image');

                $directory = "public/help_center";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $help_center->value = $filename;
                $help_center->file_path = $filepath;
                $help_center->update();
            }

            if($request->has('question')){
                foreach($request->question as $key=>$val){
                    $help_center = HelpCenter::find($key);
                    $help_center->question = $val;
                    $help_center->update();
                }

                foreach($request->answer as $index=>$value){
                    $help_center = HelpCenter::find($index);
                    $help_center->answer = $value;
                    $help_center->update();
                }

            }

            if($request->has('new_question')){
                for($i=0; $i<count($request->new_question); $i++){
                    $question = $request->new_question[$i];

                    if($request->has('new_answer')){
                        $answer = $request->new_answer[$i];
                    }

                    $help_center = new HelpCenter;
                    $help_center->key = 'faq';
                    $help_center->question = $question;
                    $help_center->answer = $answer;
                    $help_center->save();
                }
            }

            if($request->has('heading')){
                foreach($request->heading as $key=>$val){
                    $help_you = HelpYou::find($key);
                    $help_you->heading = $val;
                    $help_you->update();
                }

                foreach($request->description as $index=>$value){
                    $help_you = HelpYou::find($index);
                    $help_you->description = $value;
                    $help_you->update();
                }
            }

            if($request->hasFile('new_icon')){
                $icon = $request->file('new_icon');
                for($i=0; $i<count($icon); $i++){
                    $file = $icon[$i];

                    if($request->has('new_heading')){
                        $heading = $request->new_heading[$i];
                    }

                    if($request->has('new_description')){
                        $description = $request->new_description[$i];
                    }

                    $directory = "public/help_center";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();

                    if(isset($fileuploadData) && $fileuploadData->status == '200'){

                        $help_you = new HelpYou;
                        $help_you->media_id = $fileuploadData->id;
                        $help_you->heading = $heading;
                        $help_you->description = $description;
                        $help_you->save();

                    }elseif($fileuploadData->status == '400') {
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            $keys = [
                'title' => 'title',
                'banner_title' => 'banner_title',
                'banner_placeholder' => 'banner_placeholder',
                'main_title' => 'main_title',
                'sub_title' => 'sub_title',
                'faq_heading' => 'faq_heading',
                'faq_description' => 'faq_description',
                'banner_heading' => 'banner_heading',
                'banner_description' => 'banner_description',
                'button_text' => 'button_text',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description'
            ];

            foreach($keys as $key=>$input){
                if($request->has($input)){
                    $help_center = HelpCenter::where('key', $key)->first();
                    if($help_center){
                        $help_center->value = $request->$input;
                        $help_center->update();
                    }else{
                        $help_center = new HelpCenter;
                        $help_center->key = $key;
                        $help_center->value = $request->$input;
                        $help_center->save();
                    }
                }
            }

            if($request->bg_image_id != null){
                $help_center = HelpCenter::where('id',$request->bg_image_id)->first();
                $file_path = getFilePath($help_center->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $help_center->value = null;
                $help_center->file_path = null;
                $help_center->save();
            }

            if($request->baner_image_id != null){
                $help_center = HelpCenter::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($help_center->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $help_center->value = null;
                $help_center->file_path = null;
                $help_center->save();
            }

            if($request->btom_banner_id != null){
                $help_center = HelpCenter::where('id',$request->btom_banner_id)->first();
                $file_path = getFilePath($help_center->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $help_center->value = null;
                $help_center->file_path = null;
                $help_center->save();
            }

            if($request->helpimageId != null){
                $deleteIds = explode(',', $request->helpimageId);
                foreach($deleteIds as $id){
                    $help_you = HelpYou::where('id',$id)->with('media')->first();
                    if($help_you->media){
                        $image_path = getFilePath($help_you->media->file_path);
                        if (File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id',$help_you->media_id)->delete();

                        $help_you->media_id = null;
                        $help_you->update();
                    }
                }
            }

            if($request->removefaq != null){
                $deleteIds = explode(',', $request->removefaq);
                foreach($deleteIds as $id){
                    $help_center = HelpCenter::find($id);
                    if($help_center){
                        $help_center->delete();
                    }
                }
            }

            if($request->removehelp != null){
                $deleteIds = explode(',', $request->removehelp);
                foreach($deleteIds as $id){
                    $help_you = HelpYou::where('id',$id)->with('media')->first();
                    if($help_you->media){
                        $image_path = getFilePath($help_you->media->file_path);
                        if (File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id',$help_you->media_id)->delete();
                        $help_you->delete();
                    }

                }
            }

            return redirect()->back()->with('success', 'Data successfully updated.');
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateHelpImage(Request $request){
        if($request->image_id != null){
            $id = $request->image_id;
            if($request->hasFile('help_image')) {
                $file = $request->file('help_image');
                $directory = "public/help_center";
                $fileupload = $this->fileUploadService->upload($file, $directory);
                $fileuploadData = $fileupload->getData();

                $help_you = HelpYou::find($id);
                if(isset($fileuploadData) && $fileuploadData->status == '200'){
                    $help_you->media_id = $fileuploadData->id;
                    $help_you->update();

                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'success',
                    ];

                }elseif($fileuploadData->status == '400') {
                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'fail',
                    ];
                }

                return response()->json($response);

            }else{
                return response()->json([
                    'code' => '400',
                    'status' => 'fail',
                    'message' => 'No file uploaded',
                ], 400);
            }
        }
    }

    public function legal_document(){
        $legal = LoginRegister::where('key','legal')->first();
        return view('admin.site_meta.legal_document.legal_document',compact('legal'));
    }

    public function addLegal(Request $request){
        try{
            if($request->id != null){
                $legal = LoginRegister::find($request->id);
                $status = 'updated';
            }else{
                $legal = new LoginRegister;
                $status = 'saved';
                $legal->key = 'legal';
            }

            $legal->title = $request->page_title;
            $legal->meta_title = $request->meta_title;
            $legal->meta_description = $request->meta_description;
            $legal->main_heading = $request->heading;
            $legal->main_sub_heading = $request->sub_heading;
            $legal->save();

            if($status == 'updated'){
                return redirect()->back()->with('success','Data Successfully updated');
            }elseif($status == 'saved'){
                return redirect()->back()->with('success','Data Successfully saved');
            }
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function logos(){
        $keys = [
            'header_logo',
            'footer_logo',
            'favicon',
        ];

        $results = Setting::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'header_logo_id' => $results['header_logo']->id ?? null,
            'header_logo' => str_replace('public/', '', $results['header_logo']->file_path ?? null),
            'footer_logo_id' => $results['footer_logo']->id ?? null,
            'footer_logo' => str_replace('public/', '', $results['footer_logo']->file_path ?? null),
            'favicon_id' => $results['favicon']->id ?? null,
            'favicon' => str_replace('public/', '', $results['favicon']->file_path ?? null),
        ];
        return view('admin.site_meta.web_setting.logos',compact('data'));
    }

    public function addLogos(Request $request){
        try{
            if($request->hasFile('header_logo')){
                $file = $request->file('header_logo');
                $directory = "public/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $web_setting = Setting::where('key','header_logo')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();

            }

            if($request->hasFile('footer_logo')){
                $file = $request->file('footer_logo');
                $directory = "public/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $web_setting = Setting::where('key','footer_logo')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();

            }

            if($request->hasFile('favicon')){
                $file = $request->file('favicon');
                $directory = "public/logos";
                $filename = generateFileName($file);
                $filepath = $file->storeAs($directory, $filename);

                $web_setting = Setting::where('key','favicon')->first();
                $web_setting->value = $filename;
                $web_setting->file_path = $filepath;
                $web_setting->update();
            }

            if($request->remove_logo1 != null){
                $web_setting = Setting::where('id',$request->remove_logo1)->first();
                $file_path = getFilePath($web_setting->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $web_setting->value = null;
                $web_setting->file_path = null;
                $web_setting->save();
            }

            if($request->remove_logo2 != null){
                $web_setting = Setting::where('id',$request->remove_logo2)->first();
                $file_path = getFilePath($web_setting->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $web_setting->value = null;
                $web_setting->file_path = null;
                $web_setting->save();
            }

            if($request->favicon_img_id != null){
                $web_setting = Setting::where('id',$request->favicon_img_id)->first();
                $file_path = getFilePath($web_setting->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $web_setting->value = null;
                $web_setting->file_path = null;
                $web_setting->save();
            }

            return redirect()->back()->with('success', 'Data successfully saved.');
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


// function formatDate($date, $fallback = 'M d, Y'): string
// {
//     if (!$date) return '';

//     $format = \App\Models\Setting::where('key', 'date_format')->value('value') ?? $fallback;

//     return \Carbon\Carbon::parse($date)->format($format);
// }

}

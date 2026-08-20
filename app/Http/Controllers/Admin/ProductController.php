<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Services\FileUploadService;
use Exception;


class ProductController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService){
        $this->fileUploadService = $fileUploadService;
    }

    public function productCategory(){
        $parent_category = ProductCategory::all();
        return view('admin.products.product_categories',compact('parent_category'));
    }

    public function addProductCategory(Request $request){
        try{
            if($request->id != null){
                $productCategory = ProductCategory::find($request->id);
                $status = 'updated';
            }else{
                $productCategory = new ProductCategory;
                $status = 'saved';
            }
            $productCategory->title = $request->title;
            $productCategory->slug = $request->slug;
            $productCategory->parent_category = $request->parent_category;
            $productCategory->description = $request->description;
            $productCategory->display_type = $request->display_type;

            if($request->hasFile('thumbnail')){
                $file = $request->file('thumbnail');
        
                $fileupload = $this->fileUploadService->upload($file, 'public');
                $fileuploadData = $fileupload->getData();
                if(isset($fileuploadData) && $fileuploadData->status == '200'){

                    $productCategory->thumbnail = $fileuploadData->id;

                }elseif($fileuploadData->status == '400') {
                    return redirect()->back()->with('error', $fileuploadData->error);
                }
            }

            $productCategory->save();

            if($status == 'updated'){
                return redirect()->back()->with('success','Data Successfully updated');
            }elseif($status == 'saved'){
                return redirect()->back()->with('success','Data Successfully saved');
            }

        }catch(Exception $e){
            saveLog("Error:", "ProductController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function categories(){
        $categories = ProductCategory::with('media')->get();
        return view('admin.products.categories',compact('categories'));
    }

    public function editCategories(Request $request,$slug){
        $categories = ProductCategory::where('slug',$slug)->with('media')->first();
        $parent_category = ProductCategory::all();
        return view('admin.products.product_categories',compact('categories','parent_category'));
    }

    public function product(){
        $category = ProductCategory::all();
        return view('admin.products.create_product',compact('category'));
    }

    public function addProduct(Request $request){
        try{
            if($request->id != null){
                $product = Product::find($request->id);
                $status = 'updated';
            }else{
                $product = new Product;
                $status = 'saved';
            }
            $product->name = $request->name;
            $product->product_description = $request->description;
            $product->additional_information = $request->additional_information;
            $product->category_id = $request->product_category;
            $product->regular_price = $request->regular_price;
            $product->sale_price = $request->sale_price;
            $product->save();

            if($status == 'updated'){
                return redirect()->back()->with('success','Data Successfully updated');
            }elseif($status == 'saved'){
                return redirect()->back()->with('success','Data Successfully saved');
            }

        }catch(Exception $e){
            saveLog("Error:", "ProductController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function allproducts(){
        $products = Product::with('category')->get();
        return view('admin.products.products',compact('products'));
    }

    public function editProduct(Request $request,$id){
        $product = Product::find($id);
        $category = ProductCategory::all();
        return view('admin.products.create_product',compact('product','category'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\KnowledgeBaseCategory;
use App\Models\KnowledgeBaseArticle;
use App\Models\ArticleContent;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\MediaService;

class KnowledgeBaseController extends Controller
{

    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }


    public function knowledge_base_categories(){
        $categories =KnowledgeBaseCategory::all();
        return view('admin.knowledge_base.categories' ,compact('categories'));

    }




    public function addCategory(){
        return view('admin.knowledge_base.add_category');
    }

    public function storeCategory(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|mimetypes:image/jpeg,image/png,image/svg+xml|max:2048',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:155',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'image.required' => 'La imagen es obligatoria.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
        ]);

        $category = new KnowledgeBaseCategory;

        $category->name = $request->name;
        $category->description = $request->description;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $slug = Str::slug($request->name, '-');
        $count = KnowledgeBaseCategory::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        $category->slug = $slug;

        if($request->hasFile('image')){
            $file = $request->file('image');
            $media = $this->mediaService->uploadMedia($file, 'knowledgebase/category');

            if ($media) {
                $category->image = $media->id;
            } else {
                return redirect()->back()->with('error', 'Media upload failed.');
            }
        }

        $category->save();

        return redirect()->route('knowledge.base.category')->with('success', 'Category updated successfully');
    }


    public function updateCategory(Request $request){
        $category = KnowledgeBaseCategory::findOrFail($request->id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => $category->image ? 'nullable|image|mimes:jpeg,png,jpg,svg,gif' : 'required|mimetypes:image/jpeg,image/png,image/svg+xml|max:2048',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:155',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'image.required' => 'La imagen es obligatoria.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
        ]);

        if ($category->name !== $request->name) {
            $slug = Str::slug($request->name, '-');
            $count = KnowledgeBaseCategory::where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $category->slug = $slug;
        }

        $category->name = $request->name;
        $category->description = $request->description;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        if ($request->file('image')) {
            $file = $request->file('image');

            if ($category->image) {
                $this->mediaService->deleteMedia($category->id);
            }

            $media = $this->mediaService->uploadMedia($file, 'knowledgebase/category');

            if ($media) {
                $category->image = $media->id;
            } else {
                return redirect()->back()->with('error', 'Media upload failed.');
            }
        }

        $category->save();

        return redirect()->route('knowledge.base.category')->with('success', 'Categoría actualizada con éxito.');
    }


    public function editCategory($id){
        $category = KnowledgeBaseCategory::findOrFail($id);

        if ($category && $category->image) {
            $media = Media::where('id', $category->image)->first();
            $mediaUrl = $media ? $this->mediaService->getMediaUrl($media) : null;
        } else {
            $mediaUrl = null;
        }

        return view('admin.knowledge_base.edit_category', compact('category','mediaUrl'));
    }

    public function deleteCategoryImage(Request $request){
        $category = KnowledgeBaseCategory::findOrFail($request->id);
        // dd($category);
        if ($category->image) {

            $media = Media::where('id', $category->image)->first();
            if($media){
                $this->mediaService->deleteMedia($media->id);
                $category->image = null;
                $category->save();

                return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
            }

        }

        return response()->json(['success' => false, 'message' => 'Image not found']);
    }

    public function deleteCategory(Request $request){
        $categoryId = $request->input('category_id');
        // dd($categoryId);
        $category = KnowledgeBaseCategory::findOrFail($categoryId);
        // Delete related articles
        $category->article()->delete();
        if($category->image){
            $media = Media::where('id', $category->image)->first();
            if($media){
                $this->mediaService->deleteMedia($media->id);
            }
        }
        $category->delete();
        return redirect()->route('knowledge.base.category')->with('success', 'Categoría actualizada con éxito.');

    }


    public function knowledge_base_article(){
        $articles= KnowledgeBaseArticle::all();

        return view('admin.knowledge_base.article',compact('articles'));
    }

    public function addArticle(){

        $categories= KnowledgeBaseCategory::all();
        return view('admin.knowledge_base.add_article' ,compact('categories'));
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:knowledge_base_categories,id',
            'title' => 'required|string|max:255',
            'seo' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:155',
            'preview_title' => 'required|string|max:255',
            'preview_description' => 'required|string|max:255',
            'article_overview' => 'required|string',
        ]);

        $article = new KnowledgeBaseArticle;
        $article->category_id = $request->category_id;
        $article->title = $request->title;
        $article->seo = $request->seo;
        $article->seo_description = $request->seo_description;
        $article->meta_title = $request->meta_title;
        $article->meta_description = $request->meta_description;
        $article->preview_title = $request->preview_title;
        $article->preview_description = $request->preview_description;
        $article->article_overview = $request->article_overview;

        // Generate unique slug
        $slug = Str::slug($request->title, '-');
        $count = KnowledgeBaseArticle::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        $article->slug = $slug;

        // Save article
        $article->save();

        // Handle optional content blocks
        if ($request->has('content_heading') && $request->has('content_description')) {
            $contentData = [];

            foreach ($request->content_heading as $index => $heading) {
                $description = $request->content_description[$index] ?? '';
                if (!empty($heading) || !empty($description)) {
                    $contentData[] = [
                        'content_heading' => $heading,
                        'content_description' => $description,
                    ];
                }
            }

            foreach ($contentData as $data) {

                $articlecontent = new ArticleContent;
                $articlecontent->article_id = $article->id;
                $articlecontent->content_heading = $data['content_heading'];
                $articlecontent->content_description = $data['content_description'];
                $articlecontent->save();
            }
        }

        return redirect()->route('knowledge.base.article')->with('success', 'Article added successfully');
    }
    public function uploadEditorImage(Request $request)
    {
        //echo "<pre>";print_r($request->hasFile('upload'));die();
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            // Optional: customize filename
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Store file in storage/app/public/editor-images
            $path = $file->storeAs('editor-images', $filename, 'public');

            // Create public URL: /storage/editor-images/filename.jpg
            $url = asset('storage/' . $path);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }


    public function uploadEditorImageBase24(Request $request){
        
        if($request->hasFile('upload')){
            // get file info
            $imageFile = $request->file('upload');

            // file Content to string
            $imageData = file_get_contents($imageFile->getPathname());

            // Encode image into base64 
            $base64Image = base64_encode($imageData);

            // get image extension .png .jpg
            $imageMimeType = $imageFile->getMimeType();

            // create url for base64 Image
            $base64ImageUrl = "data:{$imageMimeType};base64," . $base64Image;
            
            return response()->json(['url' => $base64ImageUrl], 200);

        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function editArticle($id){
        $categories= KnowledgeBaseCategory::all();
        $article = KnowledgeBaseArticle::find($id);

        if ($article && $article->image) {
            $media = Media::where('id',$article->image)->first();
            $mediaUrl = $media ? $this->mediaService->getMediaUrl($media) : null;
        } else {
            $mediaUrl = null;
        }

        return view('admin.knowledge_base.edit_article', compact('categories','article','mediaUrl'));

    }

    public function updateArticle(Request $request)
    {
        $article = KnowledgeBaseArticle::findOrFail($request->id);

        $request->validate([
            'category_id' => 'required|exists:knowledge_base_categories,id',
            'title' => 'required|string|max:255',
            'preview_title' => 'required|string|max:255',
            'preview_description' => 'required|string|max:255',
            'seo' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:155',
            //'heading' => 'required|string|max:255',
            //'sub_heading' => 'required|string|max:255',
            'article_overview' => 'required|string',
            'content_heading' => 'nullable|array',
            'content_heading.*' => 'nullable|string|max:255',
            'content_description' => 'nullable|array',
            'content_description.*' => 'nullable|string',
        ]);

        // Update slug if title changed
        if ($article->title !== $request->title) {
            $slug = Str::slug($request->title, '-');
            $count = KnowledgeBaseArticle::where('slug', $slug)
                ->where('id', '!=', $article->id)
                ->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $article->slug = $slug;
        }

        $article->category_id = $request->category_id;
        $article->title = $request->title;
        $article->preview_title = $request->preview_title;
        $article->preview_description = $request->preview_description;
        $article->seo = $request->seo;
        $article->seo_description = $request->seo_description;
        $article->meta_title = $request->meta_title;
        $article->meta_description = $request->meta_description;
        //$article->heading = $request->heading;
        //$article->sub_heading = $request->sub_heading;
        $article->article_overview = $request->article_overview;

        // Image update
        if ($request->file('image')) {
            $file = $request->file('image');

            if ($article->image) {
                $this->mediaService->deleteMedia($article->id);
            }

            $media = $this->mediaService->uploadMedia($file, 'knowledgebase/articles');

            if ($media) {
                $article->image = $media->id;
            } else {
                return redirect()->back()->with('error', 'Media upload failed.');
            }
        }

        $article->save();

        // Update content blocks
        if ($request->has('content_heading') && $request->has('content_description')) {
            // Delete old content
            ArticleContent::where('article_id', $article->id)->delete();

            // Insert new content
            foreach ($request->content_heading as $index => $heading) {
                $description = $request->content_description[$index] ?? '';
                if (!empty($heading) || !empty($description)) {
                    $articleContent = new ArticleContent();
                    $articleContent->article_id = $article->id;
                    $articleContent->content_heading = $heading;
                    $articleContent->content_description = $description;
                    $articleContent->save();
                }
            }
        }

        return redirect()->route('knowledge.base.article')->with('success', 'Article updated successfully.');
    }




    public function deleteArticleImage(Request $request){
        $article = KnowledgeBaseArticle::findOrFail($request->id);
        // dd($article);
        if ($article->image) {

            $media = Media::where('id',$article->image)->first();
            if($media){
                $this->mediaService->deleteMedia($media->id);
                $article->image = null;
                $article->save();

                return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
            }

        }

        return response()->json(['success' => false, 'message' => 'Image not found']);
    }

    public function deleteArticle(Request $request){
        $articleId = $request->input('article_id');
        // dd($articleId);
        $article = KnowledgeBaseArticle::findOrFail($articleId);

        if($article->image){
            $media = Media::where('id',$article->image)->first();
            if($media){
                $this->mediaService->deleteMedia($media->id);
            }
        }
        $article->delete();
        return redirect()->route('knowledge.base.article')->with('success', 'Categoría actualizada con éxito.');

    }


}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReactionRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\PostComment;
use App\Models\PostReaction;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Comment;

class PostController extends Controller
{
    public function create(StorePostRequest $request)
    {
        $data = $request->except(['attachments', 'deleted_id']);
        $allFilePaths = [];

        try {


            DB::beginTransaction();
            $post = Post::create($data);

            if ($request->hasFile('attachments')) {

                foreach ($request['attachments'] as $file) {
                    $path = $file->store('attachment-' . $post->id, 'public');
                    $allFilePaths[] = $path;

                    $attachment = PostAttachment::create([
                        'post_id' => $post->id,
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),

                        'created_by' => $request->user()->id

                    ]);
                }


            }
            DB::commit();
            return back()->with('success','post created');

        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return response()->json(['error' => $e->getMessage()], 500);

        }



    }

    public function update(UpdatePostRequest $request,Post $post){
         $data=$request->except(['attachments','deleted_id']);
        try {

        DB::beginTransaction();
            $allFilePaths = [];

            $post->update([
                'body'=>$data['body']
            ]);
       if(isset($request['attachments'])){
           foreach ($request['attachments'] as $file) {
               $path = $file->store('attachments/' . $post->id, 'public');
               $allFilePaths[] = $path;
               PostAttachment::create([
                   'post_id' => $post->id,
                   'name' => $file->getClientOriginalName(),
                   'path' => $path,
                   'mime' => $file->getMimeType(),
                   'size' => $file->getSize(),

                   'created_by' => $request->user()->id
               ]);
           }
       }


        DB::commit();
    } catch (\Exception $e) {
foreach ($allFilePaths as $path) {
Storage::disk('public')->delete($path);
}
DB::rollBack();
throw $e;
}
    }


public function deletephoto($photo){
        $deletd=PostAttachment::find($photo);
    /** @var \Illuminate\Http\UploadedFile $path */
$path=$deletd->path;
    Storage::disk('public')->delete($path);
    $deletd->delete();


}


    public function delete(Post $post){
         if($post->user_id!=Auth::user()->id ){
             return response('not allowed');
         }
         $post->delete();
    }
    public function downloadAttachment( PostAttachment $attachment){

            return response()->download(Storage::disk('public')->path($attachment->path),$attachment->name);


    }
    public function postReaction($id ,Request $request){
        $currpost =null;
        $post=Post::find($id);
        $exist=Reaction::where('object_id',$id)->where('user_id',$request->user()->id)->first();
        if(isset($exist)){
            $exist->delete();
        }else{
            $currpost= $post->reactions()->create([
                'type' => 'like',
                'user_id'=>$request->user()->id
            ]);
        }
        $react=Reaction::where('object_id',$post->id);
        return response(['success'=>true,
            'react_number'=>$react->count(),
            'has_react'=> $currpost?->count()>0,
        ]);
;
    }
    public function commentCreate($post,Request $request){

        $request->validate([
            'comment' => ['required', 'string']
        ]);

        $currentPost = Post::findOrFail($post);

        $currentPost->comment()->create([
            'comment'=>$request->comment,
            'user_id'=>$request->user()->id,

        ]);
        $comments=PostComment::where('post_id',$post)->get();
        return response(['success'=>true,
            'comments'=>$comments,
        ],201);

    }
    public function deleteComment($comment,Request $request){
        $exsit=PostComment::where('id',$comment)->where('user_id',$request->user()->id)->first();
        if(!$exsit){
            return response([
                'error'=>'this action is not allowed'
            ]);
        }
        $exsit->delete();
        return response(['success'=>true]);

    }
    public function updateComment($comment,Request $request){
        $request->validate([
            'comment'=>'required'
        ]);
        $exsit=PostComment::where('id',$comment)->where('user_id',$request->user()->id)->first();
        if(!$exsit){
            return response([
                'error'=>'this action is not allowed'
            ]);
        }
        $exsit->update(['comment'=>$request->comment]);
        $exsit->save();
        return response(['success'=>true ,
            'comments'=>$exsit,
        ]);

    }
    public function commentReaction($comment ,Request $request){
        $createdcomment=null;
        $currcomment = PostComment::findOrFail($comment);
        $exist=Reaction::where('object_id',$comment)->where('user_id',$request->user()->id)->first();
        if(isset($exist)){
            $exist->delete();
            $hasReact = false;

        }else{
            $createdcomment= $currcomment->reactions()->create([
                'type' => 'like',
                'user_id'=>$request->user()->id
            ]);
            $hasReact = true;

        }
        $react=Reaction::where('object_id',$comment)->get();
        return response(['success'=>true,
            'react_number'=>$react->count(),
            'has_react'=> $hasReact,
            'message'=>'reaction comment'
        ],200);
    }

}

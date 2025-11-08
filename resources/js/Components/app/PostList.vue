<script setup >
import PostItem from "@/Components/app/PostItem.vue";
import AttachmentPreview from "@/Components/app/attachmentPreview.vue";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

 const props=defineProps({
    posts:Array,
     candelete:Number,
})
const showAttachmentModel=ref(false);
const  previewAttachmentPost=ref({});

function openAttachmentPreviewModal(post,indx){
console.log(indx)
    previewAttachmentPost.value={post
        ,indx
    }
    showAttachmentModel.value=true
}
const comments = ref([... (props.posts.comments || [])])

function  handelComment(commentId){
    comments.value = comments.value.filter(c => c.id !== commentId)

}
function handlePostUpdate(updatedPost) {

    console.log(updatedPost.comments.post_id);

    const postIndex = props.posts.map(c => c.id).indexOf(updatedPost.comments.post_id);


        if (postIndex !== -1) {
            props.posts[postIndex].comments = updatedPost.comments;
        }

}

</script>
<template>
    <div >

post list here
    </div>
        <PostItem  @attachmentClick="openAttachmentPreviewModal"
                   v-for="post of posts" :key="post.id" :posts="post"    @updatePost="handlePostUpdate"
                   @delete="handelComment"/>

<AttachmentPreview  v-if="previewAttachmentPost.post"
                    :attachments="previewAttachmentPost.post.attachments"
                  v-model:index="previewAttachmentPost.indx"
                    v-model="showAttachmentModel" />



</template>


<style scoped>

</style>

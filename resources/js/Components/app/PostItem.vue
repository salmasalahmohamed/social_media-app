<template>

    <div  >
        <img :src="props.posts.user?.avatar_path || '/img/default_avatar.jpg'" class="w-[48px] rounded-full">
        <div>
            <h3> {{props.posts.body}} </h3>
            <div >
                {{props.posts.deleted_by}}
            </div>
            <div>
                <div v-for="(attachment,ind) of props.posts.attachments">
                    <div  @click="openAttachment(ind)" >
                        <img  v-if="attachment" :src="attachment.url"
                              class="object-cover aspect-square"/>
                        <div> <Link :href="route('post.download',attachment)" class="nav-link">download</Link>

                        </div>

                    </div>
                    </div>
                </div>

            </div>
            <div>

                <div  >
                    <post-model   :posts="props.posts" :func="'update'"/>
                    <button  type="submit" @click="submit">delete</button>

                </div>

                <button @click="postReaction(props.posts.id)">
                    <HandThumbUpIcon class="w-5 h-5"/>
{{props.posts.has_reaction? 'unlike':'like'}}
  {{props.posts.reaction_count}}
                </button>
                <button @click="toggleCommentSection">
                    <ChatBubbleLeftRightIcon class="w-5 h-5"/>

                    comment
                </button>
            </div>
        </div>
    <div v-if="comment">
        <div>
            <textarea v-model="newcomment">
        </textarea>
            <button type="submit" @click="createComment"> create new post</button>
        </div>

<div>
    <div v-if="comments" v-for="(c, index) in comments" :key="index">
        <strong> by {{ c.user_id }}</strong>: {{ c.comment }}
        <button @click="commentReaction(c.id)">
            <HandThumbUpIcon class="w-5 h-5"/>

        </button>
        {{ c.reactions ? 'Unlike' : 'Like' }}

        <button v-if="c.user_id===authUser" @click="deleteComment(c.id)" type="button"> delete</button><br>
        <div @click="toggleCommentUpdate(c)">update</div>
        <div v-if="editingCommentId === c.id">
             <textarea v-model="newcomment">
              </textarea>
            <button  @click="updateComment(c.id)" type="button"> update</button>


        </div>

        <!--        <img :src="useroncomment.avatar_path">-->
    </div>
<!--    <div >{{ props.posts.comment.0.user_id}}</div>-->
<!--    <div>-->
<!--        {{ props.posts.comment.0.comment}}-->
<!--    </div>-->
</div>
    </div>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";

import {ChatBubbleLeftRightIcon, HandThumbUpIcon} from '@heroicons/vue/24/outline'
import PostModel from "@/Components/app/postModel.vue";
import { Link } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import {onMounted, ref} from "vue";
const props=defineProps({
    posts:Array,
    candelete:Number,
});

const authUser = usePage().props.auth.user;
const comment=ref(false);
const newcomment=ref();
const editingCommentId = ref(null)
const comments = ref([props.posts.comments])
function createComment(){
    axios.post('/comment/create/'+props.posts.id,{comment:newcomment.value})
        .then(({data}) => {
            comments.value=data.comments

            newcomment.value = null

            console.log(props.posts);
        })
        .catch(error => console.error(error));

}
function commentReaction(id){
    axios.post('/comment/reaction/'+id)
        .then(({data}) => {
            const comment = comments.value.find(c => c.id === id);
            if (comment) {
                comment.reactions = data.has_react;
        }})
        .catch(error => console.error(error));


}
function  deleteComment(id){
    axios.delete('/comment/delete/'+id)
        .then(({data}) => {
            emit('deleted', id) //

            console.log(props.posts);
        })
}
function  updateComment(id){
    axios.post('/comment/update/'+id,{comment:newcomment.value})
        .then(({data}) => {
          comments.value=data.comments
            newcomment.value = null
            emit('updatePost', { ...props.post, comments: data.comments })

            console.log(comments);
        })

}

function toggleCommentUpdate(comment) {
    if (editingCommentId.value === comment.id) {
        editingCommentId.value = null
    } else {
        editingCommentId.value = comment.id
        newcomment.value = comment.comment
    }

}
    function toggleCommentSection(){
     comment.value=!comment.value


 }
function postReaction(id){
    axios.post('/post/'+id+'/reaction')
        .then(({data}) => {props.posts.reaction_count=data.react_number
    props.posts.has_reaction=data.has_react})
        .catch(error => console.error(error));
    // console.log(props.posts.reaction_count)

}

// const nextPageUrl = ref(null)
//
// const loadPosts = async (url = 'posts') => {
//     const res = await axios.get(url)
//     posts.value.push(...res.data.data) // append posts
//     nextPageUrl.value = res.data.next_page_url
//     emit('loadpost',post)
// }
//
// const loadMore = () => {
//     if (nextPageUrl.value) {
//         loadPosts(nextPageUrl.value)
//     }
// }
//
// onMounted(() => {
//     loadPosts()
// })


let likecount=ref();
function submit(){
    router.post('/post/delete/'+props.posts.id)
}
function openAttachment(ind){
    emit('attachmentClick',props.posts,ind)
}
const emit =defineEmits(['attachmentClick','deleted','updatePost','loadpost'])
</script>

<style scoped>

</style>

<script setup>
import {Head, Link, router} from '@inertiajs/vue3';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import UserList from "/resources/js/Components/app/FollowingList.vue";
import GroupList from "@/Components/app/GroupList.vue";
import CreatePost from "@/Components/app/CreatPost.vue";
import PostList from "@/Components/app/PostList.vue";

defineProps({
    posts:Object,
    groups:Array,
    users:Object,
})


</script>

<template>
    <Head title="social media website" />
    <AuthenticatedLayout>
        <div class="grid lg:grid-cols-12 gap-3 p-4 h-full">
            <div class="lg:col-span-3 lg:order-1 h-full overflow-hidden">
                <GroupList :groups="groups.data"/>
            </div>
            <div class="lg:col-span-3 lg:order-3 h-full overflow-hidden">
                <div v-if="users" >
                    <UserList :users="users"
                              />
                </div>
                <div v-else class="text-center py-8">
                   you dont follow any one
                </div>
            </div>
            <div class="lg:col-span-3 lg:order-3 h-full overflow-hidden">
                <CreatePost/>
                <PostList :posts="posts.data" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style>

</style>

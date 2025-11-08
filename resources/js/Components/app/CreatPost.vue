<script setup>

import {ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import PostModel from "@/Components/app/postModel.vue";

const postCreating=ref(false);
const newPost=ref({
    body:''
        })
const form = useForm({
    body: '',
})

const props=defineProps({

    groupId:Number
})
</script>

<template>
    <TextInput  v-model="form.body" @click="postCreating=true"/>
        <div v-if="postCreating">
            <PostModel    :func="'create'" :groupId='groupId' />


        </div>

</template>

<style scoped>

</style>

<template>

    <div>
        <form @submit.prevent="submitForm">
            <input type="text" v-model="form.name" >
            <textarea type="text" v-model="form.about"/> >
<Checkbox v-model:checked="form.auto_approval"/>            <button type="submit"> submit </button>

        </form>
    </div>
</template>
<script setup>
import {useForm} from "@inertiajs/vue3";
import Checkbox from "@/Components/Checkbox.vue";
import axiosClient from "@/Components/axiosClient.js";
import axios from "axios";

const form =useForm({
    name:'',
    about:'',
    auto_approval:''


})
const submitForm = () => {
    axios.post('/create/group',form

    ).then(res=>{
        console.log(res)

    });
};
</script>

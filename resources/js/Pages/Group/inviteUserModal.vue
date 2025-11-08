<template>
    <Form  @submit.prevent="submit" method="post">
        <input type="email" v-model="form.email" />
        <button  type="submit">invite User</button>
    </Form>
</template>

<script setup>

import {useForm, usePage} from "@inertiajs/vue3";
const props=defineProps({

    group:Object
})


 const form=useForm({
     email:'',
 })
 function submit(){
    console.log(form.email)
     console.log('email')

     form.post(route('group.inviteUser',props.group.slug), {
         preserveScroll: true,
         onSuccess: (event) => console.log(event),
         onError: (event) => console.log(event),
     })
 }
</script>

<style scoped>

</style>

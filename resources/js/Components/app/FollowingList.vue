<template>
    <h2> my user  </h2>

    <div class="grid lg:grid-cols-12 gap-3 p-4 h-full">
        <div  v-for="user in props.users " class="lg:col-span-3 lg:order-1 h-full overflow-hidden">

            <FriendItem :user="user" :group="props.group" :acceptandrejectmodal="props.acceptandrejectmodal"
                 />
        </div>
    </div>
</template>

<script setup>
import FriendItem from "/resources/js/Components/app/FriendItem.vue";



const props=defineProps({
    users:Object,
    group:Object,
    acceptandrejectmodal:Boolean

})

</script>

<style scoped>

</style>

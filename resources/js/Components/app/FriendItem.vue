<template>

    <div class="flex items-start gap-3 mb-3 cursor-pointer hover:bg-gray-100" >
        <img src="{{user.avatar_url}}" class="w-[48px] rounded-full">
        <div @click="acceptandrejectmodal=!acceptandrejectmodal">
            <h3  class="font-black text-2xl"> {{user.name}}</h3>

        </div>
        <div v-if= "acceptandrejectmodal &&group">
            <button @click="acceptInvitation(user.id,group)" type="button">accept</button>
            <button @click="rejectInvitation(user.id,group)" type="button">reject</button>

        </div>

        <div>
            <button v-if="!acceptandrejectmodal&&group"
                    @click="removeUser(user.id,group)"

                    :disabled="group?.user_id===user.id"> delete</button>
        </div>
    </div>
</template>

<script  setup>
 import axios from "axios";

defineProps({
   user:Object,
    group:Object,
    acceptandrejectmodal:Boolean

});
function removeUser(user_id,group) {
    axios.post('/remove/user/'+user_id, group

    ).then(res=>{
        console.log(res)

    });

}
 function rejectInvitation(user_id,group){

     axios.post('/reject/invitation/'+user_id,group

     ).then(res=>{
         console.log(res)

     });
 }
 function  acceptInvitation(user_id,group){
     axios.post('/accept/invitation/'+user_id,group

     ).then(res=>{
         console.log(res)

     });

 }

</script>

<style scoped>

</style>

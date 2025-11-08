

<script setup>
import GroupListItems from "@/Components/app/GroupListItems.vue";
import GroupItem from "@/Components/app/GroupItem.vue";
import {onMounted, ref} from "vue";
import GroupDialog from "@/Components/app/GroupDialog.vue";

const open=ref(false);
onMounted(()=>{
    open.value=false
})
function dialog(){
    open.value=!open.value;
    console.log(open)

}
const props=defineProps({
    groups:Array
}
)
</script>
<template>
    <h2> my groups </h2>
    <div >
        <button @click="dialog" >
            create group
        </button>
        <div v-if="open" >
            <GroupDialog />
        </div>

    </div>
    <GroupListItems/>
    <div class="grid lg:grid-cols-12 gap-3 p-4 h-full">
        <div  v-for="group of props.groups" :key="group.id" class="lg:col-span-3 lg:order-1 h-full overflow-hidden">
            <GroupItem :group="group"/>
        </div>
    </div>
</template>
<style scoped>

</style>

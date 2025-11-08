


<script>
import {ref} from "vue";
import TextInput from "@/Components/TextInput.vue";

export default {
    name: "GroupListItems",
    components: {TextInput}
}
const  searchKeyword =ref('');

</script>
<template>
    <TextInput :model-value="searchKeyword"/>

</template>



<style scoped>

</style>

<template >

    <div>

        <img src="{{group.image}}" >

                    <Link :href="route('group.profile', group.slug)" >

            <h3> {{group.title}}</h3>
{{group.description}}



                        <div v-if="group.group_user && group.group_user.length">
                            <div v-for="user in group.group_user" :key="user.id" class="mb-2">
                                <p><strong>ID:</strong> {{ user.id }}</p>
                                <p><strong>Role:</strong> {{ user.role }}</p>
                                <p><strong>Status:</strong> {{ user.status }}</p>
                                <hr>
                            </div>
                        </div>
                        <div v-else>
                            No group user data
                        </div>





        </Link>
    </div>


</template>

<script  setup>
import {Link} from '@inertiajs/vue3'

const props=defineProps({
    group: {
        type: Object,
        required: true
    }});
console.log('Group prop:', props.group);

</script>

<style scoped>

</style>

<script setup>

const props = defineProps({
    attachments: Array,
    index: Number,
    modelValue: Boolean
});

const emit = defineEmits(['update:modelValue',"update:index"]);
function close() {
    emit('update:modelValue', false);
}
function nextAttachment() {
    if (props.index < props.attachments.length - 1) {

        emit("update:index", props.index + 1);
    }
}

function prevAttachment() {
    if (props.index > 0) {
        emit("update:index", props.index - 1);
    }
}

</script>

<template>
    <div v-if="modelValue" class="fixed inset-0 bg-black/70 flex items-center justify-center">
        <div class="bg-white p-4 rounded">
            <button @click="nextAttachment">next</button>
            <button @click="prevAttachment">pre</button>

            <p>Showing attachment {{ index++ }} of {{ attachments.length }}</p>

            <img v-if="attachments && attachments[index]" :src="attachments[index].url" class="max-w-[500px]"/>

            <button @click="close">Close</button>
        </div>
    </div>
</template>

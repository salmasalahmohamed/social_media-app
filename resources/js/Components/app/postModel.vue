<template>
    <div>
        <button class="rounded-md bg-white/10 px-2.5 py-1.5 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20" @click="open = true">Open dialog</button>
        <TransitionRoot as="template" :show="open">
            <Dialog class="relative z-10" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="" leave="ease-in duration-200" leave-from="" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-900/50 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to=" translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from=" translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-gray-800 text-left shadow-xl outline -outline-offset-1 outline-white/10 transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-500/10 sm:mx-0 sm:size-10">
                                            <ExclamationTriangleIcon class="size-6 text-red-400" aria-hidden="true" />
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <DialogTitle as="h3" class="text-base font-semibold text-white">{{func}} post</DialogTitle>
                                            <div class="mt-2">
<!--                                                <img :src="props.posts.user.avatar_path || '/img/default_avatar.jpg'" class="w-[48px] rounded-full">-->
<!--                                                <div>-->
<!--                                                    <h3> {{props.posts.user.name}}</h3>-->
<!--                                                </div>-->

<!--                                                <textarea v-model="props.posts.body"/>-->
                                                <ckeditor  :editor="editor" v-model="form.body" :config="editorConfig"></ckeditor>
                                                <div  v-if="attachmentFiles" v-for="attachment of attachmentFiles">

                                                    <img    :src="attachment.url" alt=""/>

                                                </div>
                                                <div  v-if="func==='update'" v-for="attachment2 of props.posts.attachments">
                                                    <img  v-if="attachment2" :src="attachment2.url" alt=""/>
                                                </div>
                                                <div v-if="func==='update'" v-for="attachment of computedAttachments">
                                                    <button type="button" @click="removeImage(attachment)">X</button>

                                                    <img   :src="attachment.url"

                                                          class="object-cover aspect-square"/>

<!--                                                    <template >-->
<!--                                                        <small>{{attachment.file.name}}</small>-->
<!--                                                    </template>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                    <button type="submit"
                                            class="inline-flex w-full justify-center rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-red-400 sm:ml-3 sm:w-auto" @click='submit'>submit</button>
                                    <button type="button"
                                            class="inline-flex w-full justify-center rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-red-400 sm:ml-3 sm:w-auto" >
 attach
                                        <input   @change="onAttachmentChoose" type="file" multiple >
                                    </button>

                                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20 sm:mt-0 sm:w-auto" @click="open = false" ref="cancelButtonRef">Cancel</button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import {computed, ref, watch} from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import {useForm} from "@inertiajs/vue3";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import {isImage} from "@/helper.js";
import { Inertia } from '@inertiajs/inertia'

const editor=ClassicEditor;
const props=defineProps({
    posts: {
        type: Object,
        required: true
    },
    groupId:Number,
    func:String

});



const form=useForm({
    body: String,
    attachments:[],
    deleted_id:null,
    group_id:props.groupId??null,

});
const attachmentFiles=ref([]);

async function onAttachmentChoose($event) {
    console.log($event)

    for (const file of $event.target.files) {
        const myFile = {
            file,
            url: await readFile(file)
        }
        attachmentFiles.value.push(myFile)
    }
    console.log(attachmentFiles)
    $event.target.value = null;
}
async function readFile(file) {
    return new Promise((res, rej) => {
        if (isImage(file)) {
            const reader = new FileReader();
            reader.onload = () => {
                res(reader.result)
            }
            reader.onerror = rej
            reader.readAsDataURL(file)
        } else {
            res(null)
        }
    })
}

function submit(){
    form.attachments=attachmentFiles.value.map(myFile=>myFile.file)

    if(props.posts && props.posts.id){

        form.post(route('post.update',props.posts));
    }else{
        form.post('/post/create', {
                onSuccess: () => {
                    form.reset();
                },
                onError:(errors)=>{

                }
            }

        )


    }

}
const open = ref(false)



 function removeImage(attachment){
     if(attachment.file){

         const index = attachmentFiles.value.indexOf(attachment)
         if (index !== -1) {
             attachmentFiles.value.splice(index, 1)}
         }else {


         form.deleted_id=attachment.id;
         Inertia.delete(route('photo.delete',form.deleted_id),{

         })

     }
 }

</script>

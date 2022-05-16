<template>
<label class="flex items-center lg:py-3.5 py-4 px-5" :class="{'group cursor-pointer hover:bg-light-background dark:hover:bg-4x-dark-foreground': !isHoverDisabled }">
    <div class="mr-4">
        <upload-cloud-icon v-if="type === 'file'" size="17" class="vue-feather group-hover-text-theme" />
        <folder-upload-icon v-if="type === 'folder'" size="17" class="vue-feather group-hover-text-theme" />
    </div>
    <div class="group-hover-text-theme text-left text-sm font-bold">
        {{ title }}
        
            <input
                v-if="type === 'file'"
                @click="selectTipoDocumento"
                v-show="false"
                id="file"
                type="text"
                name="files"
            />
            <input
                v-if="type === 'folder'"
                @click="selectTipoDocumento"
                v-show="false"
                id="folder"
                type="text"
                name="folders[]"
                webkitdirectory
                mozdirectory
            />
            
    </div>
            
</label>
</template>

<script>
import FolderUploadIcon from './Icons/FolderUploadIcon'
import { ValidationProvider, ValidationObserver } from 'vee-validate/dist/vee-validate.full'
import FormLabel from '../../components/Others/Forms/FormLabel'
import Spinner from '../../components/FilesView/Spinner'

import {
    UploadCloudIcon
} from 'vue-feather-icons'
import { events } from '../../bus'
export default {
    name: 'Option',
    props: ['title', 'type', 'isHoverDisabled'],
    components: {
        FolderUploadIcon,
        UploadCloudIcon,
        ValidationProvider, ValidationObserver, FormLabel, Spinner 
    },
    methods: {
        selectTipoDocumento() {
            events.$emit('popup:open', { name: 'tipo-documento', tipo_documento: this.type })
        },
        emmitFiles(e) {
            this.$uploadFiles(e.target.files)
        },
        emmitFolder(e) {
            this.$store.commit('UPDATE_UPLOADING_FOLDER_STATE', true)

            this.$uploadFiles(e.target.files)
        },
    },
}
</script>

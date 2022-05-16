<template>
<PopupWrapper name="tipo-documento">
    <!--Title-->
    <PopupHeader title="Tipo de documento" icon="edit" />

    <!--Content-->
    <PopupContent class="!overflow-initial">
        <!--Form to set sharing-->
        <ValidationObserver @submit.prevent="createTipo" ref="createForm" v-slot="{ invalid }" tag="form">
            <ValidationProvider tag="div" mode="passive" name="Language Locale" rules="required" v-slot="{ errors }">
                <AppInputText title="Select tipo documento" :error="errors[0]">
                    <SelectInput v-model="document" :options="documents" placeholder="Selecciona tipo de documento" :isError="errors[0]" />
                </AppInputText>
            </ValidationProvider>
    </PopupContent>

    <!--Actions-->
    <PopupActions>
        <ButtonBase class="w-full" @click.native="$closePopup()" button-style="secondary">
            {{ $t('cancel') }}
        </ButtonBase>
        <ButtonBase @click.native="emitirSubida" class="w-full" button-style="theme" :loading="isLoading" :disabled="isLoading">
            Subir archivo
        </ButtonBase>
    </PopupActions>
</PopupWrapper>
</template>

<script>
import AppInputText from '../Admin/AppInputText'
import SelectInput from './Forms/SelectInput'
import {
    ValidationProvider,
    ValidationObserver
} from 'vee-validate/dist/vee-validate.full'
import PopupWrapper from './Popup/PopupWrapper'
import PopupActions from './Popup/PopupActions'
import PopupContent from './Popup/PopupContent'
import PopupHeader from './Popup/PopupHeader'

import ButtonBase from '../FilesView/ButtonBase'
import {
    required
} from 'vee-validate/dist/rules'
import {
    events
} from '../../bus'
import axios from 'axios'

export default {
    name: 'CreateTipoDocumento',
    components: {
        ValidationProvider,
        ValidationObserver,
        AppInputText,
        PopupWrapper,
        PopupActions,
        PopupContent,
        PopupHeader,
        SelectInput,
        ButtonBase,
        required,
    },
    data() {
        return {
            isLoading: false,
            documents: [],
            document: '',
            tipo_documento: 'file'

        }
    },
    created() {
        this.getDocuments();
        events.$on('popup:open', (args) => {
            console.log(args)

            if (args.tipo_documento === 'folder') {
                this.tipo_documento = 'folder'
            }
        })

        // Close popup
        events.$on('popup:close', () => {

            // Restore data
            setTimeout(() => {
                this.generatedUploadRequest = undefined
                this.form = {
                    name: undefined,
                }
            }, 150)
        })
    },
    methods: {
        async emitirSubida() {
            var input = document.createElement('input');
            input.type = 'file';

            if (this.tipo_documento == 'folder') {
                input.name = 'folders[]';
                input.webkitdirectory = 'true';
                input.mozdirectory = 'true';
            } else {
                input.name = 'files[]';
            }

            input.onchange = e => {
                if (this.tipo_documento == 'folder') {
                    this.$store.commit('UPDATE_UPLOADING_FOLDER_STATE', true)
                    this.$uploadFiles(e.target.files, this.document)
                    this.$closePopup();
                } else {
                    this.$uploadFiles(e.target.files, this.document)
                    this.$closePopup();
                }
            }

            input.click();

        },
        async getDocuments() {
            axios
                .get('/api/admin/documents')
                .then((response) => {
                    response.data.data.map((e) => this.documents.push({
                        "label": e.data.attributes.name,
                        "value": e.data.id
                    }))
                })
                .catch((e) => {
                    console.log(e)
                })
        }
    },
}
</script>

<template>
<ValidationObserver @submit.prevent="createDocument" ref="createDocument" v-slot="{ invalid }" tag="form">
    <div class="card shadow-card">
        <FormLabel>Crear tipo de documento</FormLabel>

        <ValidationProvider tag="div" mode="passive" name="name" rules="required" v-slot="{ errors }">
            <AppInputText title="Nombre de documento" :error="errors[0]">
                <input v-model="document.name" placeholder="Nombre de documento" type="text" class="focus-border-theme input-dark" :class="{ '!border-rose-600': errors[0] }" />
            </AppInputText>
        </ValidationProvider>
        <AppInputText title="Fields" style="margin-bottom: 0;"></AppInputText>

        <span v-for="(field, index) in document.fields" v-bind:key="index" style="display: block ruby; width: 300px;margin-bottom: 6px;">
            <input v-model="field.name" placeholder="Ingresa el campo" type="text" class="focus-border-theme input-dark" />

            <button @click.prevent="borrarCampo(index)" style="margin-left: 7px;background: rgba(157,102,254,.1);padding: 8px 20px 8px 20px;border-radius: 6px;color: brown;font-weight: bold;">
                Borrar
            </button>
        </span>
        <button class="btnHover" @click.prevent="agregarCampo()" style="background: rgba(0, 0, 0, 0.13) none repeat scroll 0% 0%; padding: 8px 20px; border-radius: 6px; color: brown; font-weight: bold;margin-top: 11px;">
            <span style="display: block ruby;"><svg xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="vue-feather dark-text-theme feather feather-plus">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg> Agregar otro campo </span>
        </button>

    </div>
    <div class="form-group">
        <ButtonBase :disabled="isLoading" :loading="isLoading" button-style="theme" type="submit" class="w-full sm:w-auto">
            Crear tipo de documento
        </ButtonBase>
    </div>
</ValidationObserver>
</template>

<script>
import AppInputText from '../../../components/Admin/AppInputText'
import {
    ValidationProvider,
    ValidationObserver
} from 'vee-validate/dist/vee-validate.full'
import SelectInput from '../../../components/Others/Forms/SelectInput'
import ImageInput from '../../../components/Others/Forms/ImageInput'
import FormLabel from '../../../components/Others/Forms/FormLabel'
import MobileHeader from '../../../components/Mobile/MobileHeader'
import SectionTitle from '../../../components/Others/SectionTitle'
import ButtonBase from '../../../components/FilesView/ButtonBase'
import PageHeader from '../../../components/Others/PageHeader'
import {
    required
} from 'vee-validate/dist/rules'
import {
    mapGetters
} from 'vuex'
import {
    events
} from '../../../bus'
import axios from 'axios'

export default {
    name: 'Profile',
    components: {
        AppInputText,
        ValidationProvider,
        ValidationObserver,
        SectionTitle,
        MobileHeader,
        SelectInput,
        ButtonBase,
        ImageInput,
        PageHeader,
        FormLabel,
        required,
    },
    computed: {
        ...mapGetters(['roles']),
    },
    data() {
        return {
            isLoading: false,
            document: {
                fields: [],
                name: '',
            },
        }
    },
    created() {
        this.document.fields.push({
            name: ''
        });
    },
    methods: {
        async agregarCampo() {
            this.document.fields.push({
                name: ''
            });
        },
        async borrarCampo(index) {
            this.document.fields.splice(index, 1);
        },
        async createDocument() {
            const isValid = await this.$refs.createDocument.validate()

            if (this.document.fields.length == 0 || this.document.fields.every((e) => e.name == '')) {
                events.$emit('alert:open', {
                    title: "Debes de ingresar al menos un campo al tipo de documento",
                })
                return
            }

            if (!isValid) return

            this.isLoading = true

            let formData = new FormData()

            formData.append('name', this.document.name)
            formData.append('fields', JSON.stringify(this.document.fields))

            axios
                .post('/api/admin/documents', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                .then((response) => {
                    this.isLoading = false

                    events.$emit('toaster', {
                        type: 'success',
                        message: 'Tipo de documento creado.',
                    })
                    //params: {id: response.data.data.id },
                    this.$router.push({
                        name: 'Documents'
                    })
                })
                .catch((error) => {
                    if (error.response.status === 409) {

                        events.$emit('alert:open', {
                            title: error.response.data.message,
                        })

                    } else if (error.response.status === 422) {

                        if (error.response.data.errors['name']) {
                            this.$refs.createDocument.setErrors({
                                email: error.response.data.errors['name'],
                            })
                        }

                    } else {
                        events.$emit('alert:open', {
                            title: this.$t('popup_error.title'),
                            message: this.$t('popup_error.message'),
                        })
                    }

                    // End loading
                    this.isLoading = false
                })
        },
    },
}
</script>

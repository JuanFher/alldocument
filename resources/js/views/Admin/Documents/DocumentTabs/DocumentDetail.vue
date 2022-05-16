<template>
<PageTab>
    <!--Change role-->
    <div class="card shadow-card">
        <FormLabel>
            {{ $t('user_box_role.title') }}
        </FormLabel>
        <ValidationObserver ref="changeName" @submit.prevent="changeName" v-slot="{ invalid }" tag="form">
            <ValidationProvider tag="div" v-slot="{ errors }" mode="passive" name="Role" rules="required">
                <AppInputText title="Name" description="Ingresa un nombre" :error="errors[0]" :is-last="true">
                    <div class="space-y-4 sm:flex sm:space-x-4 sm:space-y-0">
                        <input :value="user.data.attributes.name" @change="name = event.target.value" placeholder="Ingresa un nombre" type="text" class="focus-border-theme input-dark disabled:text-gray-900 disabled:opacity-100" />
                        <ButtonBase :loading="isSendingRequest" :disabled="isSendingRequest" type="submit" button-style="theme" class="w-full sm:w-auto">
                           Guardar
                        </ButtonBase>
                    </div>
                </AppInputText>
            </ValidationProvider>
        </ValidationObserver>
    </div>
 
</PageTab>
</template>

<script>
import AppInputText from '../../../../components/Admin/AppInputText'
import InfoBox from '../../../../components/Others/Forms/InfoBox'
import PageTabGroup from '../../../../components/Others/Layout/PageTabGroup'
import PageTab from '../../../../components/Others/Layout/PageTab'
import {
    ValidationProvider,
    ValidationObserver
} from 'vee-validate/dist/vee-validate.full'
import SelectInput from '../../../../components/Others/Forms/SelectInput'
import FormLabel from '../../../../components/Others/Forms/FormLabel'
import ButtonBase from '../../../../components/FilesView/ButtonBase'
import SetupBox from '../../../../components/Others/Forms/SetupBox'
import {
    required
} from 'vee-validate/dist/rules'
import {
    mapGetters
} from 'vuex'
import {
    events
} from '../../../../bus'
import axios from 'axios'

export default {
    name: 'DocumentDetail',
    props: ['document'],
    components: {
        AppInputText,
        PageTabGroup,
        PageTab,
        InfoBox,
        FormLabel,
        ValidationProvider,
        ValidationObserver,
        SelectInput,
        ButtonBase,
        SetupBox,
        required,
    },
    computed: {
        ...mapGetters([]),
    },
    data() {
        return {
            isLoading: false,
            isSendingRequest: false,
            name: ''
        }
    },
    methods: {
        async changeName() {
            const isValid = await this.$refs.changeName.validate()

            if (!isValid) return

            this.isSendingRequest = true

            // Send request to get user reset link
            axios
                .post(this.$store.getters.api + '/admin/documents/' + this.$route.params.id, {
                    attributes: {
                        name: this.name,
                    },
                    _method: 'patch',
                })
                .then(() => {
                    // Reset errors
                    this.$refs.changeName.reset()

                    this.$emit('reload-document')

                    events.$emit('toaster', {
                        type: 'success',
                        message: 'Tipo de documento actualizado',
                    })
                })
                .catch(() => {
                    events.$emit('alert:open', {
                        title: this.$t('popup_error.title'),
                        message: this.$t('popup_error.message'),
                    })
                })
                .finally(() => {
                    this.isSendingRequest = false
                })
        },
    },
}
</script>

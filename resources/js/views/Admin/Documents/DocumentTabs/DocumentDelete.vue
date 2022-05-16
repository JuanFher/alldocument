<template>
    <div v-if="document" class="card shadow-card">
        <FormLabel>
            {{ $t('user_box_delete.title') }}
        </FormLabel>
        <ValidationObserver ref="deleteDocument" @submit.prevent="deleteDocument" v-slot="{ invalid }" tag="form">
            <ValidationProvider tag="div" v-slot="{ errors }" mode="passive" name="Name" rules="required">
                <AppInputText
                    :title="
                        $t('admin_page_user.label_delete_document', {
                            document: document.data.attributes.name.trim(),
                        })
                    "
                    :description="$t('user_box_delete.description')"
                    :error="errors[0]"
                    :is-last="true"
                >
                    <div class="space-y-4 sm:flex sm:space-x-4 sm:space-y-0">
                        <input
                            v-model="name"
                            :placeholder="$t('admin_page_user.placeholder_delete_user')"
                            type="text"
                            class="focus-border-theme input-dark"
                            :class="{ '!border-rose-600': errors[0] }"
                        />
                        <ButtonBase
                            :loading="isSendingRequest"
                            :disabled="isSendingRequest"
                            type="submit"
                            button-style="danger"
                            class="w-full sm:w-auto"
                        >
                            {{ $t('admin_page_user.delete_user') }}
                        </ButtonBase>
                    </div>
                </AppInputText>
            </ValidationProvider>
        </ValidationObserver>
    </div>
</template>

<script>
import AppInputText from '../../../../components/Admin/AppInputText'
import FormLabel from '../../../../components/Others/Forms/FormLabel'
import InfoBox from '../../../../components/Others/Forms/InfoBox'

import PageTabGroup from '../../../../components/Others/Layout/PageTabGroup'
import PageTab from '../../../../components/Others/Layout/PageTab'
import { ValidationProvider, ValidationObserver } from 'vee-validate/dist/vee-validate.full'
import ButtonBase from '../../../../components/FilesView/ButtonBase'
import SetupBox from '../../../../components/Others/Forms/SetupBox'
import { required, is } from 'vee-validate/dist/rules'
import { events } from '../../../../bus'
import axios from 'axios'

export default {
    name: 'DocumentDelete',
    props: ['document'],
    components: {
        AppInputText,
        FormLabel,
        InfoBox,
        PageTabGroup,
        PageTab,
        ValidationProvider,
        ValidationObserver,
        ButtonBase,
        SetupBox,
        required,
    },
    data() {
        return {
            isSendingRequest: false,
            isLoading: false,
            name: '',
        }
    },
    methods: {
        async deleteDocument() {
            // Validate fields
            const isValid = await this.$refs.deleteDocument.validate()

            if (!isValid) return

            if (this.name.trim() !== this.document.data.attributes.name.trim()) {
                this.$refs.deleteDocument.setErrors({
                    'User name': 'The name is not the same.',
                })

                return
            }

            this.isSendingRequest = true

            axios
                .post(this.$store.getters.api + '/admin/documents/' + this.$route.params.id + '/delete', {
                    name: this.name,
                    _method: 'delete',
                })
                .then((response) => {
                    if (response.status === 202) {
                        events.$emit('alert:open', {
                            emoji: '☹️',
                            title: this.$t('popup_deleted_user_aborted.title'),
                            message: this.$t('popup_deleted_user_aborted.message'),
                        })
                    }

                    if (response.status === 204) {
                        events.$emit('success:open', {
                            emoji: '👍',
                            title: this.$t('popup_deleted_user.title'),
                            message: this.$t('popup_deleted_user.message'),
                        })

                        this.$router.push({ name: 'Documents' })
                    }
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

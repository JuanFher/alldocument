<template>
<div>
    <div class="card shadow-card">
        <div class="mb-6">
            <router-link :to="{ name: 'DocumentCreate' }">
                <MobileActionButton icon="Document-plus">
                    Crear tipo de documento
                </MobileActionButton>
            </router-link>
            <!--
            <MobileActionButton @click.native="$openSpotlight('documents')" icon="search">
                {{ $t('search') }}
            </MobileActionButton>
            -->
        </div>

        <!--Datatable-->
        <DatatableWrapper @init="isLoading = false" api="/api/admin/documents" :paginator="true" :columns="columns" class="overflow-x-auto">
            <template slot-scope="{ row }">
                <!--Not a subscription-->
                <tr  class="whitespace-nowrap border-b border-dashed border-light dark:border-opacity-5">
                    <td class="py-3 pr-3 md:pr-1">
                        <!---
                        <router-link :to="{
                                    name: 'DocumentDetail',
                                    params: { id: row.data.id },
                                }">--->
                            <div class="flex items-center">

                                <div class="ml-3 pr-10">
                                    <b class="max-w-1 block overflow-hidden text-ellipsis whitespace-nowrap text-sm font-bold" style="max-width: 155px">
                                        {{ row.data.attributes.name }}
                                    </b>

                                </div>
                            </div>
                            <!--
                        </router-link>
                        --->
                    </td>

                    <td class="px-3 md:px-1">
                        <span class="text-sm font-bold">
                            {{ row.data.attributes.created_at }}
                        </span>
                    </td>
                   
                    <td class="pl-3 text-right md:pl-1">
                        <div class="flex w-full justify-end space-x-2">
                            <!--
                            <router-link class="flex h-8 w-8 items-center justify-center rounded-md bg-light-background transition-colors hover:bg-green-100 dark:bg-2x-dark-foreground" :to="{
                                        name: 'DocumentDetail',
                                        params: { id: row.data.id },
                                    }">
                                <Edit2Icon size="15" class="opacity-75" />
                            </router-link>
                            --->
                            <a href="javascript:void(0);" @click="deleteDocument(row.data.id)"><Trash2Icon style="margin-top: 8px;" size="15" class="opacity-75" /></a>

                        </div>
                    </td>
                    
                </tr>

            </template>
        </DatatableWrapper>
    </div>
    <div id="loader" v-if="isLoading">
        <Spinner></Spinner>
    </div>
</div>
</template>

<script>
import MemberAvatar from '../../components/FilesView/MemberAvatar'
import DatatableCellImage from '../../components/Others/Tables/DatatableCellImage'
import DatatableWrapper from '../../components/Others/Tables/DatatableWrapper'
import MobileActionButton from '../../components/FilesView/MobileActionButton'
import MobileHeader from '../../components/Mobile/MobileHeader'
import SectionTitle from '../../components/Others/SectionTitle'
import ButtonBase from '../../components/FilesView/ButtonBase'
import PageHeader from '../../components/Others/PageHeader'
import ColorLabel from '../../components/Others/ColorLabel'
import Spinner from '../../components/FilesView/Spinner'
import {
    Trash2Icon,
    Edit2Icon
} from 'vue-feather-icons'
import {
    mapGetters
} from 'vuex'
import { events } from '../../bus'

export default {
    name: 'Documents',
    components: {
        DatatableCellImage,
        MobileActionButton,
        DatatableWrapper,
        MemberAvatar,
        SectionTitle,
        MobileHeader,
        Trash2Icon,
        PageHeader,
        ButtonBase,
        ColorLabel,
        Edit2Icon,
        Spinner,
    },
    computed: {
        ...mapGetters(['config']),
        columns() {
            return {
                metered: [{
                        label: this.$t('Document'),
                        field: 'name',
                        sortable: true,
                    },
                  
                    {
                        label: this.$t('created_at'),
                        field: 'created_at',
                        sortable: true,
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        sortable: true,
                    },
                
                ],
                fixed: [{
                        label: this.$t('Document'),
                        field: 'name',
                        sortable: true,
                    },
                  
                    {
                        label: this.$t('created_at'),
                        field: 'created_at',
                        sortable: true,
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        sortable: true,
                    },
                   
                ],
                none: [{
                        label: this.$t('Document'),
                        field: 'name',
                        sortable: true,
                    },
                  
                    {
                        label: this.$t('created_at'),
                        field: 'created_at',
                        sortable: true,
                    },
                    {
                        label: this.$t('actions'),
                        field: 'actions',
                        sortable: true,
                    },
                  
                ],
            } [this.config.subscriptionType]
        },
    },
    data() {
        return {
            isLoading: true,
        }
    },
    methods: {
        async deleteDocument(id) {
            this.isLoading = true;
            axios
                .post(this.$store.getters.api + '/admin/documents/' + id + '/delete', {
                    _method: 'delete',
                })
                .then((response) => {
                    if (response.status === 202) {
                        events.$emit('alert:open', {
                            emoji: '☹️',
                            title: "No se pudo eliminar",
                            message: "No se pudo eliminar",
                        })
                    }

                    if (response.status === 204) {
                        events.$emit('success:open', {
                            emoji: '👍',
                            title: "Tipo de documento fue eliminado",
                            message: "Tipo de documento fue eliminado con éxito",
                        })

                        //this.$router.push({ name: 'Documents' })
                        this.$router.go()
                    }
                })
                .catch(() => {
                    events.$emit('alert:open', {
                        title: this.$t('popup_error.title'),
                        message: this.$t('popup_error.message'),
                    })
                })
                .finally(() => {
                    this.isLoading = false
                })
        }
    }
}
</script>

<template>
<div v-if="isVisible" @keyup.esc="exitSpotlight" @click.exact.self="exitSpotlight" tabindex="-1" class="busqueda fixed left-0 right-0 bottom-0 top-0 z-50 z-50 h-full w-full bg-white dark:bg-dark-background md:absolute md:bg-dark-background md:bg-opacity-[0.35] dark:md:bg-opacity-[0.45]">
    <div class="relative z-50 mx-auto w-full overflow-y-auto md:mt-8 md:max-w-xl md:rounded-xl md:bg-white md:shadow-xl dark:md:bg-2x-dark-foreground 2xl:mt-20">
        <!--Query bar-->
        <div class="flex items-center px-5 py-2">
            <AppInputText title="Select tipo documento" style="margin: 0;padding: 0;">
                <SelectInput v-model="document" :options="documents" placeholder="Selecciona tipo de documento" />
            </AppInputText>
        </div>
        <div class="flex items-center px-5 py-2">
            <input id="status" type="checkbox" v-model="status">
            <label for="status" style="margin-left: 10px;">Pendiente de indexar</label>
        </div>
        <div class="z-50 mx-auto flex items-center px-5 py-4">

            <div class="relative mr-4">
                <div v-if="isLoading" class="spinner-icon origin-center translate-y-2.5 scale-50 transform">
                    <Spinner />
                </div>

                <search-icon :class="{ 'opacity-0': isLoading }" size="22" class="magnify dark-text-theme text-theme vue-feather" />
            </div>

            <!--Text search field-->
            <input class="w-full border-none bg-transparent text-lg font-semibold placeholder-gray-700 focus:outline-none dark:placeholder-gray-400 sm:text-xl" v-model="query" @keydown.delete="undoFilter" @keydown.enter="showSelected" @keydown.meta="showByShortcut" @keydown.ctrl="showByShortcut" @keyup.down="onPageDown" @keyup.up="onPageUp" type="text" :placeholder="$t('spotlight_search')" ref="searchInput" />

            <!--Desktop searchbar hint-->
            <div v-if="!$isMobile()" class="mr-2">
                <span class="text-sm text-gray-400">esc</span>
            </div>

            <!--Mobile close icon-->
            <div v-if="$isMobile()" @click="exitSpotlight" class="cursor-pointer">
                <x-icon size="22" class="close" />
            </div>
        </div>

        <!--Results-->
        <div v-if="isNotEmptyQuery" class="relative z-50 px-4 pb-4">

            <!--Show results-->
            <CategoryName v-if="results.length !== 0">
                <div v-for="(item, index) in results" v-bind:key="index">
                    <!---
                    <router-link :to="{ name: 'Files', params: { id: item.basename }}">
                        -->
                    <p class="result-option" @click="goTo(item)">{{ item.data.attributes.name }}</p>
                    <!--
                    </router-link>
                    -->
                </div>
            </CategoryName>

            <!--Show Empty message-->
            <span v-if="results.length === 0" class="p-2.5 text-sm text-gray-700 dark:text-gray-400">
                {{ $t('messages.nothing_was_found') }}
            </span>
        </div>

        <!--Hints-->
        <KeyboardHints />
    </div>
</div>
</template>

<style>
.busqueda .input-options {
    position: unset !important;
}

.busqueda .select {
    width: 530px !important;
}

.busqueda .result-option {
    font-size: 16px;
    font-weight: normal;
    padding: 5px;
    border-bottom: 1px solid #b0aba424;
}

.busqueda .result-option:hover {
    background: #f0f0ef40;
}
</style>

<script>
import {
    events
} from '../../bus'
import ItemList from '../FilesView/ItemList'
import MemberAvatar from '../FilesView/MemberAvatar'
import Spinner from '../FilesView/Spinner'
import CategoryName from './CategoryName'
import FilterSuggestion from './FilterSuggestion'
import KeyboardHints from './KeyboardHints'
import axios from 'axios'
import AppInputText from '../Admin/AppInputText'
import SelectInput from '../Others/Forms/SelectInput.vue'
import {
    debounce
} from 'lodash'
import {
    FolderPlusIcon,
    SmileIcon,

    BoxIcon,
    CreditCardIcon,
    DatabaseIcon,
    DollarSignIcon,
    FileTextIcon,
    GlobeIcon,
    GridIcon,
    HardDriveIcon,
    HomeIcon,
    LinkIcon,
    Maximize2Icon,
    MonitorIcon,
    MoonIcon,
    PowerIcon,
    SearchIcon,
    SettingsIcon,
    Trash2Icon,
    TrashIcon,
    UploadCloudIcon,
    UserCheckIcon,
    UserPlusIcon,
    UsersIcon,
    XIcon,
} from 'vue-feather-icons'
import {
    mapGetters
} from 'vuex'

export default {
    name: 'Spotlight',
    components: {
        SelectInput,
        AppInputText,
        FolderPlusIcon,
        SmileIcon,
        KeyboardHints,
        CreditCardIcon,
        GridIcon,
        CategoryName,
        FilterSuggestion,
        Maximize2Icon,
        TrashIcon,
        MoonIcon,
        PowerIcon,
        HardDriveIcon,
        UploadCloudIcon,
        FileTextIcon,
        DollarSignIcon,
        GlobeIcon,
        MonitorIcon,
        BoxIcon,
        UsersIcon,
        UserCheckIcon,
        LinkIcon,
        SettingsIcon,
        HomeIcon,
        DatabaseIcon,
        UserPlusIcon,
        MemberAvatar,
        Trash2Icon,
        SearchIcon,
        ItemList,
        Spinner,
        XIcon,
    },
    computed: {
        ...mapGetters(['config', 'user', 'sharedDetail']),

        isAdmin() {
            return this.user.data.attributes.role === 'admin'
        },
        metaKeyIcon() {
            return this.$isApple() ? '⌘' : 'Ctrl'
        },
        isNotEmptyQuery() {
            return this.query !== ''
        },
        isEmptyQuery() {
            return this.query === '' || this.query === undefined
        },
    },
    data() {
        return {
            isVisible: false,
            isLoading: false,
            query: null,
            index: 0,
            documents: [],
            document: null,
            results: [],
            status: false

        }
    },
    watch: {

        query(query) {
            if (query === '' || typeof query === 'undefined') this.results = []
            this.searchFilesAndFolders()
        },
        document(newQuestion, oldQuestion) {
            if (this.document === '' || typeof this.document === 'undefined') this.results = []
            this.searchFilesAndFolders()
        },
        status(newQuestion, oldQuestion) {
            if (this.status === '' || typeof this.status === 'undefined') this.results = []
            this.searchFilesAndFolders()
        },

    },
    methods: {
        async searchFilesAndFolders() {
            axios
                .get('/api/admin/files/' + this.query + '/' + this.document + '/' + this.status)
                .then((response) => {
                    if (response.data.data.length > 0) {
                        this.results = response.data.data
                    } else {
                        this.results = []
                    }
                })
                .catch((e) => {
                    console.log(e)
                })
        },
        showByShortcut(e) {
            // Preserve select and reload native shortcut
            if (!['a', 'r', 'v'].includes(e.key)) {
                e.preventDefault()
            }

            const allowedRange = this.results.length + this.actions.length

            // Allow only numbers within result range
            if (Number.isInteger(parseInt(e.key)) && parseInt(e.key) < allowedRange) {
                this.index = parseInt(e.key)

                this.showSelected()
            }
        },
        showSelected() {
            let index = this.index
            let resultIndex = index - this.actions.length

            // Open Action
            if (this.actions.length > 0 && index < this.actions.length) {
                this.openAction(this.actions[index])
                return
            }

            // Open user
            if (this.activeFilter === 'users') {
                this.openUser(this.results[resultIndex])
            }

            // Open file or folder
            if (!this.activeFilter) {
                this.openItem(this.results[resultIndex])
            }
        },
        openAction(arg) {
            if (arg.action.type === 'route') {
                this.$router.push({
                    name: arg.action.value
                })
            }

            if (arg.action.type === 'function') {
                if (arg.action.value === 'toggle-emoji') {
                    this.$store.dispatch('toggleEmojiType')
                }

                if (arg.action.value === 'toggle-grid-list') {
                    this.$store.dispatch('togglePreviewType')
                }

                if (arg.action.value === 'dark-mode') {
                    this.$store.dispatch('toggleThemeMode')
                }

                if (arg.action.value === 'full-screen-mode') {
                    this.$store.dispatch('toggleNavigationBars')
                }

                if (arg.action.value === 'log-out') {
                    this.$store.dispatch('logOut')
                }

                if (arg.action.value === 'empty-trash') {
                    this.$emptyTrashQuietly()
                }

                if (arg.action.value === 'create-team-folder') {
                    this.$createTeamFolder()
                }

                if (arg.action.value === 'create-file-request') {
                    this.$createFileRequest()
                }
            }

            this.exitSpotlight()
        },
        openUser(user) {
            this.$router.push({
                name: 'UserDetail',
                params: {
                    id: user.data.id
                },
            })

            this.exitSpotlight()
        },
        openItem(file) {
            // Show folder
            if (file.data.type === 'folder') {
                if (this.$isThisRoute(this.$route, ['Public'])) {
                    this.$router.push({
                        name: 'Public',
                        params: {
                            token: this.sharedDetail.data.attributes.token,
                            id: file.data.id,
                        },
                    })
                } else if (file.data.attributes.isTeamFolder) {
                    let route = file.data.relationships.user.data.id === this.user.data.id ?
                        'TeamFolders' :
                        'SharedWithMe'

                    this.$router.push({
                        name: route,
                        params: {
                            id: file.data.id
                        },
                    })
                } else {
                    this.$router.push({
                        name: 'Files',
                        params: {
                            id: file.data.id
                        },
                    })
                }
            }

            if (file.data.type !== 'folder') {
                // Show file
                if (['video', 'audio', 'image'].includes(file.data.type) || file.data.attributes.mimetype === 'pdf') {
                    this.$store.commit('ADD_TO_FAST_PREVIEW', file)

                    events.$emit('file-preview:show')
                } else {
                    this.$downloadFile(
                        file.data.attributes.file_url,
                        file.data.attributes.name + '.' + file.data.attributes.mimetype
                    )
                }
            }

            this.exitSpotlight()
        },
        findResult: debounce(function (value) {
            // Prevent empty searching
            if (value === '' || typeof value === 'undefined') return

            this.isLoading = true

            // Get route
            let route = this.$store.getters.sharedDetail ?
                `/api/browse/search/${this.$router.currentRoute.params.token}` :
                '/api/browse/search'

            axios
                .get(`${route}?filter=${this.activeFilter}`, {
                    params: {
                        query: value
                    },
                })
                .then((response) => {
                    // Show user result
                    if (this.activeFilter === 'users') {
                        this.results = response.data.data
                    }

                    // Show file result
                    if (!this.activeFilter) {
                        let files = response.data.files.data
                        let folders = response.data.folders.data

                        this.results = folders.concat(files)
                    }
                })
                .catch(() => this.$isSomethingWrong())
                .finally(() => (this.isLoading = false))
        }, 150),
        exitSpotlight() {
            this.actions = []
            this.results = []
            this.query = ''
            this.isVisible = false
        },
        onPageDown() {
            let results = this.results.length
            let actions = this.actions.length

            let totalResultLength = results + actions - 1

            if (this.index < totalResultLength) this.index++
        },
        onPageUp() {
            if (this.index > 0) this.index--
        },
        setFilter(filter) {
            // Set active filter
            this.activeFilter = filter

            // Set default values
            this.results = []
            this.query = ''

            this.$nextTick(() => this.$refs.searchInput.focus())
        },
        undoFilter() {
            if (this.activeFilter && this.query === '' && this.backspaceHits !== 2) {
                this.backspaceHits++
            }

            if (this.backspaceHits === 2) {
                this.removeFilter()
            }
        },
        removeFilter() {
            // Set default values
            this.activeFilter = undefined
            this.backspaceHits = 0
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
        },
        goTo(item) {

            //this.$store.commit('LOADING_STATE', { data: JSON.parse(JSON.stringify(arr)), loading: false })
            this.$store.commit('CLIPBOARD_CLEAR')
            console.log({
                ...item
            })
            this.$store.commit('ADD_ITEM_TO_CLIPBOARDV2', {
                ...item
            })
            events.$emit('file-preview:show')
            this.exitSpotlight()
            this.document = ''
            this.results = []
        }
    },
    created() {
        this.getDocuments();
        events.$on('spotlight:show', (filter) => {
            this.isVisible = true
            this.activeFilter = filter

            this.$nextTick(() => {
                if (this.$refs.searchInput) this.$refs.searchInput.focus()
            })
        })

        events.$on('spotlight:hide', () => this.exitSpotlight())
    },
}
</script>

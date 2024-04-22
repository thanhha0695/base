<template>

    <div>

        <user-popup
            v-if="isPopupActive"
            :is-popup-active.sync="isPopupActive"
            :role-options="roles"
            :status-options="statusOption"
            :user="user"
            :is-update.sync="isUpdate"
            @update-data="updateData"
        />

        <!-- Filters -->
        <users-list-filters
            :role-filter.sync="roleFilter"
            :status-filter.sync="statusFilter"
            :role-options="roles"
            :status-options="statusOption"
        />

        <!-- Table Container Card -->
        <b-card
            no-body
            class="mb-0"
        >

            <div class="m-2">

                <!-- Table Top -->
                <b-row>

                    <!-- Per Page -->
                    <b-col
                        cols="12"
                        md="6"
                        class="d-flex align-items-center justify-content-start mb-1 mb-md-0"
                    >
                        <label>Show</label>
                        <v-select
                            v-model="perPage"
                            :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                            :options="perPageOptions"
                            :clearable="false"
                            class="per-page-selector d-inline-block mx-50"
                        />
                        <label>entries</label>
                    </b-col>

                    <!-- Search -->
                    <b-col
                        cols="12"
                        md="6"
                    >
                        <div class="d-flex align-items-center justify-content-end">
                            <b-form-input
                                v-model="searchQuery"
                                class="d-inline-block mr-1"
                                placeholder="Search..."
                            />
                            <b-button
                                variant="primary"
                                @click="isPopupActive = true"
                            >
                                <span class="text-nowrap">Add User</span>
                            </b-button>
                        </div>
                    </b-col>
                </b-row>

            </div>

            <b-table
                ref="refUserListTable"
                class="position-relative"
                :items="userList"
                responsive
                :fields="tableColumns"
                :per-page="perPage"
                :current-page="currentPage"
                primary-key="id"
                show-empty
                empty-text="No matching records found"
            >

                <!-- Column: User -->
                <template #cell(user)="data">
                    <b-media vertical-align="center">
                        <template #aside>
                            <b-avatar
                                size="32"
                                :src="data.item.avatar"
                                :text="avatarText(data.item.name)"
                            />
                        </template>
                        <b-link
                            class="font-weight-bold d-block text-nowrap"
                        >
                            {{ data.item.name }}
                        </b-link>
                        <small class="text-muted">@{{ data.item.username }}</small>
                    </b-media>
                </template>

                <!-- Column: Role -->
                <template #cell(role)="data">
                    <div class="text-nowrap">
                        <span class="align-text-top text-capitalize">{{ data.item.roleName }}</span>
                    </div>
                </template>

                <!-- Column: Status -->
                <template #cell(status)="data">
                    <b-badge
                        pill
                        :variant="`light-${resolveUserStatusVariant(data.item.status)}`"
                        class="text-capitalize"
                    >
                        {{ data.item.status === 1 ? 'Active' : 'Inactive' }}
                    </b-badge>
                </template>

                <!-- Column: Actions -->
                <template #cell(actions)="data">
                    <b-dropdown
                        variant="link"
                        no-caret
                        :right="$store.state.appConfig.isRTL"
                    >

                        <template #button-content>
                            <feather-icon
                                icon="MoreVerticalIcon"
                                size="16"
                                class="align-middle text-body"
                            />
                        </template>
                        <b-dropdown-item
                            v-if="isCan.update"
                            :to="{ name: 'manage-users-permission', params: { userId: data.item.id } }"
                            exact-active-class="exact-active"
                        >
                            <feather-icon icon="LockIcon" />
                            <span class="align-middle ml-50">Permission</span>
                        </b-dropdown-item>

                        <b-dropdown-item v-if="isCan.update" @click.prevent="editUser(data.item)">
                            <feather-icon icon="EditIcon" />
                            <span class="align-middle ml-50">Edit</span>
                        </b-dropdown-item>

                        <b-dropdown-item v-if="isCan.delete" @click.prevent="deleteUser(data.item.id)">
                            <feather-icon icon="TrashIcon" />
                            <span class="align-middle ml-50">Delete</span>
                        </b-dropdown-item>
                    </b-dropdown>
                </template>

            </b-table>
            <div v-if="userList.length > 0" class="mx-2 mb-2">
                <b-row>

                    <b-col
                        cols="12"
                        sm="6"
                        class="d-flex align-items-center justify-content-center justify-content-sm-start"
                    >
                        <span class="text-muted">Showing {{ dataMeta.from }} to {{ dataMeta.to }} of {{ dataMeta.of }} entries</span>
                    </b-col>
                    <!-- Pagination -->
                    <b-col
                        cols="12"
                        sm="6"
                        class="d-flex align-items-center justify-content-center justify-content-sm-end"
                    >

                        <b-pagination
                            v-model="currentPage"
                            :total-rows="userList.length"
                            :per-page="perPage"
                            first-number
                            last-number
                            class="mb-0 mt-1 mt-sm-0"
                            prev-class="prev-item"
                            next-class="next-item"
                        >
                            <template #prev-text>
                                <feather-icon
                                    icon="ChevronLeftIcon"
                                    size="18"
                                />
                            </template>
                            <template #next-text>
                                <feather-icon
                                    icon="ChevronRightIcon"
                                    size="18"
                                />
                            </template>
                        </b-pagination>

                    </b-col>

                </b-row>
            </div>
        </b-card>
    </div>
</template>

<script>
import {
   BCard, BRow, BCol, BFormInput, BButton, BTable, BMedia, BAvatar, BLink,
   BBadge, BDropdown, BDropdownItem, BPagination,
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import { avatarText } from '@core/utils/filter'
import UsersListFilters from './Filter.vue'
import UserPopup from './Popup.vue'
import callApi from "@/axios";
import { callDialogConfirm, perPageOptionDefault } from "@/helpers";

export default {
    name: 'User-List',
    components: {
        UsersListFilters,
        UserPopup,
        BCard,
        BRow,
        BCol,
        BFormInput,
        BButton,
        BTable,
        BMedia,
        BAvatar,
        BLink,
        BBadge,
        BDropdown,
        BDropdownItem,
        BPagination,
        vSelect,
    },
    data() {
        return {
            totalUsers: 0,
            perPage: 10,
            currentPage: 1,
            users: [],
            searchQuery: '',
            dataMeta: {
                from: 1,
                to: 1,
                of: 1
            },
            roles: [],
            statusOption: [
                {
                    value: 1,
                    label: 'Active'
                },
                {
                    value: 0,
                    label: 'Inactive'
                }
            ],
            user: {
                username: '',
                email: '',
                name: '',
                roleId: '',
                status: 1,
                id: ''
            },
            isPopupActive: false,
            isUpdate: false,
            roleFilter: null,
            statusFilter: null
        }
    },
    computed: {
        userList() {
            const users = this.users
            let data = users.filter(item => {
                return item.name.includes(this.searchQuery) || item.email.includes(this.searchQuery)
            })
            if (this.isNotEmpty(this.statusFilter)) {
                data = data.filter((item) => {
                    return item.status === this.statusFilter
                })
            }
            if (this.isNotEmpty(this.roleFilter)) {
                data = data.filter((item) => {
                    return item.roleId === this.roleFilter
                })
            }
            const perPage = this.perPage
            const currentPage = this.currentPage
            const count = this.users.length
            this.dataMeta = {
                from: perPage * (currentPage - 1) + (count ? 1 : 0),
                to: perPage * (currentPage - 1) + perPage,
                of: count,
            }
            return data
        }
    },
    watch: {
        isPopupActive(val) {
            if (val === false) {
                this.user = {
                    username: '',
                    email: '',
                    name: '',
                    roleId: '',
                    status: 1,
                    id: ''
                }
                this.isUpdate = false
            }
        }
    },
    setup() {
        const resolveUserStatusVariant = status => {
            if (status === 1) return 'success'
            if (status === 0) return 'secondary'
            return 'primary'
        }
        const tableColumns = [
            'user',
            'email',
            'role',
            'status',
            'actions'
        ]
        const perPageOptions = perPageOptionDefault()
        return {
            avatarText,
            resolveUserStatusVariant,
            tableColumns,
            perPageOptions
        }
    },
    created() {
        this.fetchUsers()
    },
    methods: {
        fetchUsers() {
            callApi.get('manage/users').then((response) => {
                this.users = response.users
                const roles = response.roles
                const self = this
                Object.values(roles).forEach(function (role) {
                    self.roles.push({ label: role.name, value: role.id })
                })
                this.roles.unshift({ label: 'Vui lòng chọn quyền', value: null })
            })
        },
        updateData(item) {
            const isUpdate = item.isUpdate;
            if (!isUpdate) {
                this.users.unshift(item)
                return
            }
            const index = this.users.findIndex(x => x.id === item.id)
            // this.users[index] = item
            this.users.splice(index, 1, item)
        },
        editUser(item) {
            this.user = item
            this.isUpdate = true
            this.isPopupActive = true
        },
        deleteUser(id) {
            callDialogConfirm(() => {
                callApi.delete(`manage/users/${ id }/delete`).then((response) => {
                    const index = this.users.findIndex(x => x.id === id)
                    this.users.splice(index, 1)
                }).catch((error) => {})
            })
        }
    }
}
</script>

<style lang="scss" scoped>
    .per-page-selector {
        width: 90px;
    }
</style>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>

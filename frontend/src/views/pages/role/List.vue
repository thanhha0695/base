<template>

    <div>

        <role-popup
            v-if="isPopupActive"
            :is-popup-active.sync="isPopupActive"
            :parent-role-options="parentRoles"
            :manage-options="users"
            :is-update.sync="isUpdate"
            :role="role"
            @update-data="updateData"
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
                                v-if="isCan.create"
                                variant="primary"
                                @click="isPopupActive = true"
                            >
                                <span class="text-nowrap">Add Role</span>
                            </b-button>
                        </div>
                    </b-col>
                </b-row>
            </div>
            <b-table
                ref="refRoleListTable"
                class="position-relative"
                :items="rolesList"
                responsive
                :fields="tableColumns"
                :per-page="perPage"
                :current-page="currentPage"
                primary-key="id"
                show-empty
                empty-text="No matching records found"
            >
                <!-- Column: id -->
                <template #cell(id)="data">
                    <div class="text-nowrap">
                        <span class="align-text-top text-capitalize">{{ data.item.id }}</span>
                    </div>
                </template>

                <!-- Column: name -->
                <template #cell(name)="data">
                    <div class="text-nowrap">
                        <span class="align-text-top text-capitalize">{{ data.item.name }}</span>
                    </div>
                </template>

                <!-- Column: name -->
                <template #cell(manager)="data">
                    <div class="text-nowrap">
                        <span class="align-text-top text-capitalize">{{ data.item.manage }}</span>
                    </div>
                </template>

                <!-- Column: description -->
                <template #cell(description)="data">
                    <div class="text-nowrap">
                        <span class="align-text-top text-capitalize">{{ data.item.description }}</span>
                    </div>
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
                        <b-dropdown-item v-if="(isCan.update && userAuthenticate.roleId !== data.item.id) || isPrivilege" :to="{ name: 'manage-roles-permission', params: { roleId: data.item.id } }">
                            <feather-icon icon="LockIcon" />
                            <span class="align-middle ml-50">Permission</span>
                        </b-dropdown-item>

                        <b-dropdown-item v-if="(isCan.update && userAuthenticate.roleId !== data.item.id) || isPrivilege" @click.prevent="editRole(data.item)">
                            <feather-icon icon="EditIcon" />
                            <span class="align-middle ml-50">Edit</span>
                        </b-dropdown-item>

                        <b-dropdown-item v-if="(isCan.delete && userAuthenticate.roleId !== data.item.id) || isPrivilege" @click.prevent="deleteRole(data.item.id)">
                            <feather-icon icon="TrashIcon" />
                            <span class="align-middle ml-50">Delete</span>
                        </b-dropdown-item>
                    </b-dropdown>
                </template>

            </b-table>
            <div v-if="rolesList.length > 0" class="mx-2 mb-2">
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
                            :total-rows="rolesList.length"
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
import RolePopup from './Popup.vue'
import callApi from '@/axios'
import { callDialogConfirm, perPageOptionDefault } from '@/helpers'
import { mapGetters } from 'vuex'

export default {
    name: 'Role-List',
    components: {
        RolePopup,
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
            page: 1,
            perPage: 25,
            currentPage: 1,
            users: [],
            searchQuery: '',
            dataMeta: {
                from: 0,
                to: 0,
                of: 0
            },
            roles: [],
            role: {
                description: '',
                name: '',
                parentId: '',
                manageId: '',
                id: ''
            },
            isPopupActive: false,
            isUpdate: false,
            parentRoles: [],
        }
    },
    created() {
        this.fetchRoles()
    },
    computed: {
        ...mapGetters({
            userAuthenticate: 'user/getUser'
        }),
        rolesList() {
            const roles = this.roles
            const perPage = this.perPage
            const currentPage = this.currentPage
            const rolesData = roles.filter(item => {
                return item.name.includes(this.searchQuery)
            })
            const count = rolesData.length
            this.dataMeta = {
                from: perPage * (currentPage - 1) + (count ? 1 : 0),
                to: perPage * (currentPage - 1) + perPage,
                of: count,
            }
            return rolesData
        }
    },
    watch: {
        isPopupActive(val) {
            if (val === false || this.isUpdate === false && val === true) {
                this.dataDefault()
                this.isUpdate = false
            }
        }
    },
    setup() {
        const tableColumns = [
            'id',
            'name',
            'manage',
            'description',
            'actions'
        ]
        const perPageOptions = perPageOptionDefault()
        return {
            tableColumns,
            perPageOptions,
        }
    },
    methods: {
        fetchRoles() {
            callApi.get('manage/roles').then((response) => {
                const roles = response.roles
                const users = response.users
                const self = this
                self.roles = roles
                Object.values(users).forEach(function (user) {
                    self.users.push({ label: user.name, value: user.id })
                })
                Object.values(roles).forEach(function (role) {
                    self.parentRoles.push({ label: role.name, value: role.id })
                })
            })
        },
        updateData(item) {
            const isUpdate = item.isUpdate;
            if (!isUpdate) {
                this.roles.unshift(item)
                return
            }
            const index = this.roles.findIndex(x => x.id === item.id)
            this.roles.splice(index, 1, item)
        },
        editRole(item) {
            this.role = item
            this.isUpdate = true
            this.isPopupActive = true
        },
        deleteRole(id) {
            callDialogConfirm(() => {
                callApi.delete(`manage/roles/${ id }/delete`).then((response) => {
                    const index = this.users.findIndex(x => x.id === id)
                    this.roles.splice(index, 1)
                }).catch((error) => {})
            })
        },
        dataDefault() {
            const isManageId = this.userAuthenticate.isManage
            const parentId = isManageId ? this.userAuthenticate.roleId : ''
            this.role = {
                description: '',
                name: '',
                parentId: parentId,
                manageId: '',
                id: ''
            }
        },
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

<template>
    <b-card
        no-body
    >
        <b-card-body>
            <b-card-title>Role Permissions</b-card-title>
        </b-card-body>
        <b-card-body class="row">
            <b-col cols="12" md="6">
                <div class="row">
                    <b-card-text class="font-weight-bold">Client: </b-card-text>
                    <v-select
                        v-model="clientId"
                        class="col-md-6"
                        :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                        :options="clients"
                        :reduce="val => val.value"
                        :clearable="false"
                        input-id="icon"
                        placeholder="Select ..."
                    />
                </div>
            </b-col>
            <b-col cols="12" md="6">
                <div class="d-flex align-items-center justify-content-end">
                    <b-form-input
                        v-model="searchQuery"
                        class="d-inline-block mr-1"
                        placeholder="Search..."
                    />
                </div>
            </b-col>
        </b-card-body>
        <b-table
            :items="permissions"
            :fields="fieldTable"
            class="mb-0"
        >

            <template #cell(module)="data">
                {{ data.item.name }}
            </template>
            <template #cell(read)="data" class="cursor-pointer">
                <b-form-checkbox
                    v-model="data.item.view.action"
                    :disabled="data.item.view.isDisable"
                    :checked="data.item.view.action"
                    @change="val => changeView(val, data.item)"
                />
            </template>
            <template #cell(create)="data">
                <b-form-checkbox
                    v-model="data.item.create.action"
                    :disabled="data.item.create.isDisable"
                    :checked="data.item.create.action"
                />
            </template>
            <template #cell(update)="data">
                <b-form-checkbox
                    v-model="data.item.update.action"
                    :disabled="data.item.update.isDisable"
                    :checked="data.item.update.action"
                />
            </template>
            <template #cell(delete)="data">
                <b-form-checkbox
                    v-model="data.item.delete.action"
                    :disabled="data.item.delete.isDisable"
                    :checked="data.item.delete.action"
                />
            </template>
        </b-table>
        <!-- Form Actions -->
        <div class="d-flex justify-content-center align-items-center mt-2 mb-2">
            <b-button
                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                variant="primary"
                class="mr-2"
                type="submit"
                @click.prevent="savePermission"
            >
                Save
            </b-button>
            <b-button
                v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                :to="{ name: 'manage-roles' }"
                type="button"
                variant="outline-secondary"
            >
                Back
            </b-button>
        </div>
    </b-card>
</template>

<script>
import {
  BCard, BTable, BCardBody, BCardTitle, BCardSubTitle, BFormCheckbox,
  BButton, BCol, BFormInput, BCardText
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import callApi from "@/axios"
import Ripple from 'vue-ripple-directive'

export default {
  name: 'Role-Permission',
  components: {
    BCard, BTable, BCardBody, BCardTitle, BCardSubTitle,
    BFormCheckbox, BButton, BCol, BFormInput, BCardText,
    vSelect
  },
  data () {
    return {
      rolePermissions: [],
      clients: [],
      clientId: 1,
      parentPermissions: [],
      searchQuery: ''
    }
  },
  directives: {
    Ripple
  },
  created() {
    this.fetchTools()
  },
  computed: {
    permissions() {
      const rolePermissions = Object.values(this.rolePermissions)
      const parentPermissions = this.parentPermissions
      const isPrivilege = this.isPrivilege
      let data = []
      rolePermissions.forEach(tool => {
        if (this.isNotEmpty(tool.uri)) {
          const toolId = tool.id
          let disableRead = false
          let disableWrite = false
          let disableEdit = false
          let disableDel = false
          if (!isPrivilege) {
            const actionParent = parentPermissions[toolId]
            disableRead =  !actionParent.view
            disableWrite = !actionParent.create
            disableEdit = !actionParent.update
            disableDel = !actionParent.delete
            if (disableRead) {
              return
            }
          }
          data[toolId] = this.processDataTool(tool, isPrivilege, disableRead, disableWrite, disableEdit, disableDel)
        }
      })
      // const rs = data.filter(i => i !== undefined)
      return data.filter(item => {
        if (item !== undefined) {
          const nameLower = item.name.toLowerCase()
          const searchLower = this.searchQuery.toLowerCase()
          return nameLower.includes(searchLower)
        }
      })
    }
  },
  watch: {
    clientId() {
      this.fetchTools()
    }
  },
  methods: {
      fetchTools() {
                const paramRoute = this.$route.params
                const roleId = paramRoute.roleId
                callApi.get(`manage/roles/${ roleId }/permission`, {
                    params: {
                        client_id: this.clientId
                    }
                }).then((response) => {
                    this.rolePermissions = response.rolePermissions
                    this.parentPermissions = response.parentPermissions
                    const clients = response.clients
                    Object.values(clients).forEach(client => {
                        this.clients.push({ value: client.id, label: client.name })
                    })
                }).catch((err) => {})
            },
            processDataTool(tool, isPrivilege, disableView, disableCreate, disableUpdate, disableDel) {
                const isDisabled = tool.action.view === false
                return {
                    id: tool.id,
                    name: tool.name,
                    clientId: tool.clientId,
                    view: {
                        action: isPrivilege ? true : tool.action.view,
                        isDisable: disableView,
                        isParentDisable: disableView,
                    },
                    create: {
                        action: isPrivilege ? true : tool.action.create,
                        isDisable: isDisabled ? isDisabled : disableCreate,
                        isParentDisable: disableCreate,
                    },
                    update: {
                        action: isPrivilege ? true : tool.action.update,
                        isDisable: isDisabled ? isDisabled : disableUpdate,
                        isParentDisable: disableUpdate,
                    },
                    delete: {
                        action: isPrivilege ? true : tool.action.delete,
                        isDisable: isDisabled ? isDisabled : disableDel,
                        isParentDisable: disableDel,
                    },
                }
            },
            savePermission() {
                const roleId = this.$route.params.roleId
                const data = this.permissions.map(permission => {
                    return {
                        tool_id: permission.id,
                        client_id: permission.clientId,
                        action: {
                            view: permission.view.action,
                            update: permission.update.action,
                            create: permission.create.action,
                            delete: permission.delete.action
                        },
                        name: permission.name
                    }
                })
                callApi.put(`manage/roles/${ roleId }/permission/update`, { permissions: data }).then(response => {
                }).catch(err => {})
            },
            changeView(val, item) {
                const index = this.permissions.findIndex(i => item.id === i.id)
                const permission = this.permissions[index]
                const data = this.processDataDisableCheckBox(val, permission)
                this.permissions[index] = data
            },
            checkAll(val) {
                let action = {
                    action: true,
                    isDisable: false,
                }
                if (!val) {
                    action = {
                        action: false,
                        isDisable: true
                    }
                }
                this.permissions.map(item => {
                    item.view = action
                    item.update = action
                    item.create = action
                    item.delete = action
                    return item
                })
            },
            processDataDisableCheckBox(valCheckBox, item) {
                if (!valCheckBox) {
                    item.view.isDisable = false
                    item.update.isDisable = true
                    item.create.isDisable = true
                    item.delete.isDisable = true
                    item.view.action = false
                    item.update.action = false
                    item.create.action = false
                    item.delete.action = false
                    return item
                }
                item.view.action = true
                item.view.isDisable = item.view.isParentDisable
                item.update.isDisable = item.update.isParentDisable
                item.create.isDisable = item.create.isParentDisable
                item.delete.isDisable = item.delete.isParentDisable
                return item
            }
        },
        setup() {
            const fieldTable = [
                'Module',
                'Read',
                'Create',
                'Update',
                'Delete'
            ]
            return {
                fieldTable,
            }
        },
    }
</script>

<style>

</style>

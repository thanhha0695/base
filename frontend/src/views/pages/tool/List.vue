<template>
  <div>
    <div class="vx-row pt-50">
      <div class="vx-col md:w-2/5 sm:w-1/2 w-full mb-1">
        <b-card class="min-h-card card-list-sidebar" title="">
          <div class="min-h-card">
            <div class="mb-75">
              <h5 class="w-full text-center">
                Danh sách Tools
                <feather-icon icon="EditIcon" size="16" class="pointer hover-primary ml-50 mb-top--2"/>
              </h5>
            </div>
            <div class="mb-75 px-75 form-group row">
              <b-card-text class="col-sm-1 d-flex align-items-center font-weight-bold">Client: </b-card-text>
                <v-select
                  v-model="clientId"
                  class="col-sm-4"
                  :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                  :options="clientOptions"
                  :reduce="val => val.value"
                  :clearable="false"
                  input-id="icon"
                  placeholder="Select ..."
                />
              </div>
              <div>
                <draggable
                  v-model="parentData"
                  class="list-group list-group-flush cursor-pointer"
                  :move="(event) => onMove(event)"
                  @end="saveMove()"
                  v-bind="dragOptions"
                  @start="drag = true"
                  tag="ul"
                >
                  <transition-group
                    type="transition"
                      :name="drag ? 'flip-list' : null"
                  >
                      <b-list-group-item
                          v-for="listItem in findDataParent"
                          :key="listItem.id"
                          @click.prevent="openList(listItem.id, listItem.name)"
                          tag="li"
                          v-b-tooltip.hover.focus.v-secondary
                          :title="!openMenu.includes(listItem.id) ? 'Drag and drop to move menu position' : ''"
                          variant="outline-secondary"
                      >
                          <div class="text-item flex justify-between items-center row">
                              <div class="flex items-center col-sm-10">
                                  <b-card-text class="font-weight-bold mb-0">
                                      <feather-icon v-if="listItem.icon" :icon="listItem.icon"/>
                                      <feather-icon v-else icon="ChevronRightIcon"/>
                                      {{ listItem.name }}
                                  </b-card-text>
                              </div>
                              <div class="flex col-sm-2">
                                  <feather-icon
                                      v-if="isCan.update"
                                      icon="EditIcon"
                                      size="16"
                                      class="pointer hover-primary ml-50 mb-top--2"
                                      @click.stop="openPopup(listItem, false)"
                                  />
                                  <feather-icon
                                      v-if="isCan.delete"
                                      icon="Trash2Icon"
                                      size="15"
                                      class="ml-50 line-11 hover-danger"
                                      @click.stop="deleteTool(listItem)"
                                  />
                              </div>
                          </div>
                          <div
                              v-if="listItem.uri === null || listItem.uri === ''"
                              :style="openMenu.includes(listItem.id) ? 'max-height: none; overflow: auto;' : 'max-height: 0; overflow: hidden;'"
                              :key="listItem.id"
                          >
                              <ul style="padding: 10px 0 0 10px;">
                                  <b-list-group-item v-for="item in listItem.chilrden" :key="item.id" tag="li">
                                      <div class="text-item flex justify-between items-center row">
                                          <div class="flex items-center col-sm-10">
                                              <b-card-text class="font-weight-bold mb-0">
                                                  <feather-icon :icon="item.icon || 'CircleIcon'" size="10"/>
                                                  {{ item.name }}
                                              </b-card-text>
                                          </div>
                                          <div class="flex-column col-sm-2">
                                              <feather-icon
                                                  v-if="isCan.update"
                                                  icon="EditIcon"
                                                  size="16"
                                                  class="pointer hover-primary ml-50 mb-top--2"
                                                  @click.stop="openPopup(item, false)"
                                              />
                                              <feather-icon
                                                  v-if="isCan.delete"
                                                  :class="{'disable': false}"
                                                  icon="Trash2Icon" size="15"
                                                  class="ml-50 hover-danger"
                                                  @click.stop="deleteTool(item)"
                                              />
                                          </div>
                                      </div>
                                  </b-list-group-item>
                                  <div class="mt-2 d-flex justify-content-center align-items-center">
                                      <feather-icon
                                          v-if="isCan.create"
                                          width="20px"
                                          height="20px"
                                          class="cursor-pointer bg-gradient-success"
                                          icon="PlusSquareIcon"
                                          @click.stop="openPopup(listItem, true)"
                                      />
                                  </div>
                              </ul>
                          </div>
                      </b-list-group-item>
                    </transition-group>
                </draggable>
                  <div class="mt-2 d-flex justify-content-center align-items-center">
                      <feather-icon
                          v-if="isCan.create"
                          width="20px"
                          height="20px"
                          v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                          v-b-tooltip.hover.focus.v-secondary
                          title="Add Menu"
                          variant="outline-secondary"
                          class="cursor-pointer bg-gradient-success"
                          icon="PlusSquareIcon"
                          @click.stop="openPopup"
                      />
                  </div>

              </div>
              <popup-tool
                  v-if="isPopupActive"
                  :is-update.sync="isUpdate"
                  :is-popup-active.sync="isPopupActive"
                  :icon-options="iconOptions"
                  :tool="tool"
                  @update-data-tool="updateDataTool"
              ></popup-tool>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import {
  BButton,
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCollapse,
  BListGroupItem,
  VBTooltip
} from 'bootstrap-vue'
import vSelect from 'vue-select';
import callApi from "@/axios";
import PopupTool from "@/views/pages/tool/Popup";
import {callDialogConfirm} from "@/helpers";
import draggable from "vuedraggable";
import Ripple from "vue-ripple-directive";
import { mapGetters } from 'vuex'

export default {
  name: 'List-Tool',
  components: {
    PopupTool,
    vSelect,
    BCard,
    BCardText,
    draggable,
    BListGroupItem,
    BCollapse,
    BCardBody,
    BCardHeader,
    BButton,
  },
  directives: {
    'b-tooltip': VBTooltip,
     Ripple,
  },
  data() {
    return {
      type: 'parent',
      dataTools: [],
      clientId: '',
      clientOptions: [],
      isPopupActive: false,
      isUpdate: false,
      iconOptions: [],
      openMenu: [],
      tool: {},
      drag: false,
      isMove: false,
      paramMove: {},
      parentData: [],
    }
  },
  computed: {
    dragOptions() {
      return {
        animation: 200,
        group: 'description',
        disabled: !this.isCan.update,
        ghostClass: 'ghost-drag'
      }
    },
    findDataParent() {
      const items = this.dataTools
      let all = []
      items.forEach((item, index) => {
        let condition = item.id === item.parentId
        if (!this.userAuthenticate.isPrivilege && item.action !== undefined) {
          condition = condition && item.action.view === true
        }
        if (condition) {
          const position = item.position
          item.chilrden = this.findGroup(items, item.id)
            all[position] = item
          }
        })
        const data = all.filter(item => item !== undefined)
        this.parentData = data
        if (this.clientId === this.userAuthenticate.clientId) {
          this.$store.dispatch('user/setTool', items)
        }
        return data
      },
    },
  created() {
    this.getTools()
  },
  watch: {
    isPopupActive(val) {
      if (val === false) {
        this.isUpdate = false
        this.defaultDataPopup()
      }
    }
  },
  methods: {
    getTools() {
      callApi.get('manage/tools', { params: { client_id: this.clientId } }).then((response) => {
        const tools = response.tools
        const clients = response.clients
        this.dataTools = Object.values(tools)
        Object.values(clients).forEach(client => {
          this.clientOptions.push({
            value: client.id,
            label: client.name
          })
        })
        this.clientId = clients[0].id
      }).catch((error) => {})
    },
    updateDataTool(item) {
      const isUpdate = item.isUpdate
      if (!isUpdate) {
        this.dataTools.push(item)
        return
      }
      const index = this.tools.findIndex(x => x.id === item.id)
      this.dataTools.splice(index, 1, item)
    },
    findGroup(items, id) {
      return items.filter(function (item, index) {
        return item.parentId === id && item.id !== item.parentId
      })
    },
    openList(id, name) {
      if (this.openMenu.includes(id)) {
        const index = this.openMenu.findIndex(i => i === id)
        this.openMenu.splice(index, 1)
        return
      }
      this.openMenu.push(id)
    },
    onMove(event) {
      const oldIndex = event.draggedContext.index
      const newIndex = event.draggedContext.futureIndex
      const dataParent = this.findDataParent
      this.paramMove = {
        client_id: dataParent[oldIndex].clientId,
        start: {
          id: dataParent[oldIndex].id,
          position: dataParent[oldIndex].position,
        },
        end: {
          id: dataParent[newIndex].id,
          position: dataParent[newIndex].position,
        },
      }
      this.isMove = oldIndex !== newIndex
    },
    saveMove() {
      this.drag = false
      if (this.isMove) {
        callApi.put('manage/tools/move', this.paramMove).then((response) => {
          this.dataTools = response
        }).catch((error) => {})
      }
    },
    deleteTool(item) {
      callDialogConfirm(() => {
        callApi.delete(`manage/tools/${ item.id }/destroy`, { params: { client_id: item.clientId } }).then((response) => {
          const index = this.dataTools.findIndex(i => i.id === item.id)
          let listChild = []
          listChild = this.dataTools.filter((itemTool) => {
            return itemTool.parentId === item.id
          })
          if (listChild !== undefined) {
            listChild.forEach(child => {
              const indexChild = this.dataTools.findIndex(val => val.id === child.id)
              this.dataTools.splice(indexChild, 1)
            })
          }
          this.dataTools.splice(index, 1)
        }).catch((error) => {})
      }, 'danger', 'Bạn có chắc chắn muốn xóa tool này?')
    },
    openPopup(item, isAdd = true) {
      if (isAdd === true) {
        const parentId = item.parentId !== undefined ? item.parentId : ''
        this.defaultDataPopup(parentId)
        this.isUpdate = false
        this.isPopupActive = true
        return
      }
      this.tool = {
        clientId: item.clientId,
        name: item.name,
        icon: item.icon,
        parentId: item.parentId,
        uri: item.uri,
        id: item.id
      }
      this.isUpdate = true
      this.isPopupActive = true
    },
    defaultDataPopup(parentId = '') {
      this.tool = {
        parentId: parentId,
        name: '',
        uri: '',
        icon: '',
        clientId: this.clientId,
        id: '',
      }
    }
  }
}

</script>

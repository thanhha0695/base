import Vue from 'vue'
import { mapGetters } from "vuex"

export default Vue.extend({
  data() {
    return {
    }
  },
  computed: {
    ...mapGetters({
    menuSidebar: 'user/getTools',
    userAuthenticate: 'user/getUser'
  }),
  isPrivilege() {
    return this.userAuthenticate.isPrivilege
  },
  isCan() {
    const isPrivilege = this.userAuthenticate.isPrivilege
    if (isPrivilege) {
      return {
        create: true,
        update: true,
        delete: true,
        view: true
      }
    }
    const pathname = window.location.pathname
    const currentUri = pathname.replace('/admin', '')
    const tools = this.menuSidebar
    let acl = {
      create: false,
      update: false,
      delete: false,
      view: false
    }
    Object.values(tools).forEach(tool => {
      if (tool.uri === currentUri) {
        acl = tool.action
      }
    })
    return acl
    },
  },
  methods: {
    isNotEmpty(val) {
      return val !== '' && val !== null && val !== undefined
    },
    sidebarMenu(isHorizotal = false) {
      let data = []
      const isPrivilege = this.userAuthenticate.isPrivilege
      const toolArray = Object.values(this.menuSidebar)
      toolArray.forEach((tool) => {
        let condition = tool.id === tool.parentId
        if (!isPrivilege) {
          condition = condition && tool.action.view === true
        }
        if (condition) {
          const item = {}
          const position = tool.position
          const path = tool.uri
          if (isHorizotal) {
            item.header = tool.name
          }
          item.title = tool.name
          item.icon = tool.icon
          item.route = { path: path }
          item.children = []
          toolArray.filter((val) => {
            let conditionChild = val.parentId === tool.parentId && val.id !== val.parentId
            if (!isPrivilege) {
              conditionChild = conditionChild && val.action.view === true
            }
            if (conditionChild) {
               item.children.push({
                 title: val.name,
                 icon: val.icon,
                 route: { path: val.uri }
               })
            }
          })
          if (item.children.length === 0 && path !== null) {
            delete item.children
          }
          data[position] = item
        }
      })
      return data.filter(el => { return el !== undefined })
    }
  }
})

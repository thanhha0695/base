const user = {
    namespaced: true,
    state: {
      accessToken: '',
      user: {
        username: '',
        name: '',
        gender: '',
        role: 'admin',
        avatar: '',
        email: '',
        status: 1,
        phoneNumber: '',
        birthday: '',
        note: '',
        isPrivilege: false,
        isManage: false,
        roleName: '',
        roleId: ''
    },
    tools: []
  },
  getters: {
    getUser: state => state.user,
    getAccessToken: state => state.accessToken,
    getTools: state => state.tools
  },
  mutations: {
    SET_USER(state, payload) {
      state.user = payload
    },
    SET_TOKEN(state, payload) {
      state.accessToken = payload
    },
    SET_TOOL(state, payload) {
      state.tools = payload
    },
    UPDATE_PROFILE(state, payload) {
      state.user.avatar = payload.avatar
      state.user.gender = payload.gender
      state.user.birthday = payload.birthday
      state.user.email = payload.email
      state.user.name = payload.name
      state.user.phoneNumber = payload.phoneNumber
    }
  },
  actions: {
    setUser({ commit }, payload) {
      commit('SET_USER', payload)
    },
    setToken({ commit }, payload) {
      commit('SET_TOKEN', payload)
    },
    setTool({ commit }, payload) {
      commit('SET_TOOL', payload)
    },
    updateProfile({ commit }, payload) {
      commit('UPDATE_PROFILE', payload)
    }
  }
}

export default user

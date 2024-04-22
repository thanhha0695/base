const common = {
  namespaced: true,
  state: {
    loading: false,
    bg: false,
    bgProcess: false,
    completed: 0,
    typeForm: '',
    toast : false,
  },
  getters: {
    getLoading: state => state.loading,
    getBg: state => state.bg,
    getBgProcess: state => state.bgProcess,
    getCompleted: state => state.completed,
    getTypeForm: state => state.typeForm,
     getToast : state => state.toast
  },
  mutations: {
     SET_LOADING(state, data) {
        state.loading = data
     },
     SET_BG(state, data) {
        state.bg = data
     },
     SET_BG_PROCESS(state, data) {
       state.bgProcess = data
       if (!data) {
         state.completed = 0
            state.typeForm = ''
         }
       },
      SET_COMPLETED(state, data) {
        state.completed = data
      },
      SET_TYPE_FORM(state, data) {
        state.typeForm = data
      },
      SET_TOAST(state, data) {
        state.toast = data
      }
  },
  actions: {
    setLoading({ commit }, data) {
      commit("SET_LOADING", data)
    },
    showLoading({ commit }) {
        commit("SET_LOADING", true)
    },
    hideLoading({ commit }) {
      commit("SET_LOADING", false)
    },
    showBg({ commit }) {
      commit("SET_BG", true)
    },
    hideBg({ commit }) {
      commit("SET_BG", false)
    },
    showBgProcess({ commit }) {
      commit("SET_BG_PROCESS", true)
    },
    hideBgProcess({ commit }) {
      commit("SET_BG_PROCESS", false)
    },
    setCompleted({ commit }, payload) {
      commit("SET_COMPLETED", payload)
    },
    setTypeForm({ commit }, payload) {
      commit("SET_TYPE_FORM", payload)
    },
    showToast({commit}) {
      commit("SET_TOAST", true)
    },
    hideToast({ commit }) {
      commit("SET_TOAST", false)
    },
  }
}

export default common;

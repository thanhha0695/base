import store from '@/store/index'

export const setToken = (accessToken) => {
    localStorage.setItem('_ac', accessToken)
    store.dispatch('user/setToken', accessToken).then(r => {})
}

export const getToken = () => {
    const accessToken = store.state.user.accessToken
    return localStorage.getItem('_ac') || accessToken
}

export const logout = () => {
    store.dispatch('user/setTool', []).then(r => {})
    store.dispatch('user/setToken', '').then(r => {})
    store.dispatch('user/setUser', {}).then(r => {})
    localStorage.clear()
}

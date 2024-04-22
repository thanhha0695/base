import axios from 'axios'
import { keysToCamel, defaultHandleError, defaultHandleSuccess } from '@/./helpers'
import { logout, getToken } from '@/auth/auth';

const callApi = axios.create({
  baseURL: 'http://project-demo.com/api/',
  headers: {
    'Authorization': `Bearer ${ getToken() }`,
    'Content-type': 'application/json',
    'Accept': 'application/json'
  },
})

export function setupInterceptors({dispatch}, router) {
  let requestsPending = 0
  const actionScope = 'common'
  const req = {
    pending: () => {
      requestsPending++
        dispatch(`${actionScope}/showLoading`)
      },
    done: () => {
      if (requestsPending > 0) requestsPending--
      if (requestsPending <= 0) {
        dispatch(`${actionScope}/hideLoading`)
      }
    },
  }

  callApi.interceptors.request.use(function (config) {
    // Do something before request is sent
    if (!config.runInBackground) {
      req.pending()
    }

    return config
  }, function (error) {
    // Do something with request error
    // console.error(error)
    req.done()
    return Promise.reject(error)
  })

  // Add a response interceptor
  callApi.interceptors.response.use(function (response) {
    if (!response.config.runInBackground || response.config.upTime) {
      req.done()
    }
    defaultHandleSuccess(response)
    let _res = response.data.data
    return keysToCamel(_res)
    // return response;
  }, function (error) {
    req.done()
    const status = error.response.status
    if (status === 401) {
      const uriLogin = '/auth/login'
      if (window.location.pathname === `/admin${ uriLogin }`) {
        defaultHandleError(error)
        return Promise.reject(error)
      }
      logout()
      const redirectUri = router.history._startLocation
      if (uriLogin !== redirectUri) {
        localStorage.setItem('redirectUri', redirectUri)
      }
      router.push(uriLogin)
    }
    defaultHandleError(error)
    return Promise.reject(error)
  })
}

export default callApi

export default [
  {
    path: '/error-404',
    name: 'error-404',
    component: () => import('@/views/error/Error404.vue'),
    meta: {
      layout: 'full',
      resource: 'Auth',
      action: 'read',
    },
  },
  {
    path: '/auth/login',
    name: 'auth-login',
    component: () => import('@/views/pages/authentication/Login.vue'),
    meta: {
      layout: 'full',
      resource: 'Auth',
      redirectIfLoggedIn: true,
    },
  },
  {
    path: '/pages/miscellaneous/not-authorized',
    name: 'misc-not-authorized',
    component: () => import('@/views/pages/miscellaneous/NotAuthorized.vue'),
    meta: {
      layout: 'full',
      resource: 'Auth',
    },
  },
  {
    path: '/pages/miscellaneous/under-maintenance',
    name: 'misc-under-maintenance',
    component: () => import('@/views/pages/miscellaneous/UnderMaintenance.vue'),
    meta: {
      layout: 'full',
    },
  },
  {
    path: '/pages/miscellaneous/error',
    name: 'misc-error',
    component: () => import('@/views/pages/miscellaneous/Error.vue'),
    meta: {
      layout: 'full',
    },
  },
  {
    path: '/pages/account-setting',
    name: 'pages-account-setting',
    component: () => import('@/views/pages/account-setting/AccountSetting.vue'),
    meta: {
      pageTitle: 'Account Settings',
      breadcrumb: [
        {
          text: 'Pages',
        },
        {
          text: 'Account Settings',
          active: true,
        },
      ],
    },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/pages/dashboard/List'),
  },
  {
    path: '/manage/users',
    name: 'manage-users',
    component: () => import('@/views/pages/user/List'),
  },
  {
    path: '/manage/tools',
    name: 'manage-tools',
    component: () => import('@/views/pages/tool/List'),
  },
  {
    path: '/manage/roles',
    name: 'manage-roles',
    component: () => import('@/views/pages/role/List'),
  },
  {
    path: '/manage/users/:userId/permission',
    name: 'manage-users-permission',
    component: () => import('@/views/pages/user/Permission'),
  },
  {
    path: '/manage/roles/:roleId/permission',
    name: 'manage-roles-permission',
    component: () => import('@/views/pages/role/Permission'),
  },
]

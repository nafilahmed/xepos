import { createRouter, createWebHistory } from 'vue-router'
import store from '@/store'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', redirect: '/company' },
    {
      path: '/',
      component: () => import('../layouts/default.vue'),
      children: [
        {
          name: 'company',
          path: 'company',
          component: () => import('../pages/company.vue'),
          meta: {
            middleware: "auth",
            title: `Company`
          },
        },
        {
          name: 'employee',
          path: 'employee',
          component: () => import('../pages/employee.vue'),
          meta: {
            middleware: "auth",
            title: `Employee`
          },
        },
      ],
    },
    {
      path: '/',
      component: () => import('../layouts/blank.vue'),
      children: [
        {
          name: "login",
          path: 'login',
          component: () => import('../pages/login.vue'),
          meta: {
            middleware: "guest",
            title: `Login`
          }
        },
        {
          name: 'register',
          path: 'register',
          component: () => import('../pages/register.vue'),
          meta: {
            middleware: "guest",
            title: `Login`
          }
        },
        {
          path: '/:pathMatch(.*)*',
          component: () => import('../pages/[...all].vue'),
        },
      ],
    },
  ],
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title
  if (to.meta.middleware == "guest") {
      if (store.state.auth.authenticated) {
          next({ name: "company" })
      }
      next()
  } else {
      if (store.state.auth.authenticated) {
          next()
      } else {
          next({ name: "login" })
      }
  }
})

export default router

import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import store from '@/store'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
  },
  {
    path: '/ruang-praktikum',
    name: 'ruang-praktikum',
    component: () => import('../views/RuangPraktikum.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInRight animate__slow",
      leaveClass:"animate__animated animate__fadeOutLeft"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'home'
        })
      }
      next();
    }
  },
  {
    path: '/labroom/:class_id',
    name: 'labroom',
    props:true,
    component: () => import('../views/LabRoom.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInRight animate__slow",
      leaveClass:"animate__animated animate__fadeOutLeft"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'home'
        })
      }
      next();
    }
  },

  

]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router

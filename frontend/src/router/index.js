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
    path: '/profile',
    name: 'profile',
    component: () => import('../views/ProfileView.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/user/super',
    name: 'super',
    component: () => import('../views/SuperUser.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/user/guru',
    name: 'UserGuru',
    component: () => import('../views/UserGuru.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/user/siswa',
    name: 'UserSiswa',
    component: () => import('../views/UserSiswa.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/rombel',
    name: 'Rombel',
    component: () => import('../views/RombelView.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/slide',
    name: 'Slide',
    component: () => import('../views/SlideView.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/user/role',
    name: 'role',
    component: () => import('../views/RoleView.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/inventaris',
    name: 'inventaris',
    component: () => import('../views/InventarisView.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/katalog',
    name: 'katalog',
    component: () => import('../views/KatalogView.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/pinjam-lab',
    name: 'pinjam-lab',
    component: () => import('../views/PinjamLab.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/pinjam-alat',
    name: 'pinjam-alat',
    component: () => import('../views/PinjamAlat.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/pinjam-lain',
    name: 'pinjam-lain',
    component: () => import('../views/PinjamLain.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/peminjaman/lab',
    name: 'peminjaman-lab',
    component: () => import('../views/PeminjamanLab.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/peminjaman/alat',
    name: 'peminjaman-alat',
    component: () => import('../views/PeminjamanAlat.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/peminjaman/lainnya',
    name: 'peminjaman-lainnya',
    component: () => import('../views/PeminjamanLain.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/modul-lkpd',
    name: 'modul-lkpd',
    component: () => import('../views/ModulLkpd.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/data-siswa',
    name: 'data-siswa',
    component: () => import('../views/DataSiswa.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/praktikum',
    name: 'praktikum',
    component: () => import('../views/PraktikumView.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/ruang-praktikum/:class_id',
    name: 'ruang-praktikum',
    props:true,
    component: () => import('../views/RuangPraktikum.vue'),
    meta:{
      enterClass:"animate__animated animate__fadeInUp animate__slow",
      leaveClass:"animate__animated animate__fadeOut"
    },
    beforeEnter: (to, from, next)=>{
      if(!store.getters['auth/authenticated']){
        return next({
          name:'login'
        })
      }
      next();
    }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
    meta:{
      enterClass:"animate__animated animate__zoomIn animate__slow",
      leaveClass:"animate__animated animate__zoomOut animate__slow"
    }
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router

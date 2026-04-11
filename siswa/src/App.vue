<template>
  <q-layout view="hHh LpR fFf">
    <q-header class=" text-green-7 shadow" :class="visibleClass">
      <div class="row">
        <q-toolbar class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8 constrain bg-transparent">
          <q-toolbar-title>
            <img src="./assets/eipa2.png" style="max-width:200px" class="gt-xs"/>
          </q-toolbar-title>
          <q-space/>
          <q-btn v-if="authenticated" label="ruang praktikum" rounded dense no-caps style="width:150px" to="/ruang-praktikum" unelevated/>
          <q-btn v-if="authenticated" icon="o_home" size="sm" class="q-mx-sm" to="/" round unelevated />
          <q-icon v-if="authenticated" name="o_notifications" size="sm" class="q-mx-sm"/>
          <q-btn v-if="!authenticated" label="Login" icon="o_login" color="green-7" rounded flat >
            <q-menu>
              <q-list bordered separator style="min-width:200px">
                <q-item >
                  <q-item-section>
                    <q-input  outlined v-model="username" placeholder="username" dense />
                    <q-input  outlined v-model="password" placeholder="password" type="password" class="q-my-sm" dense />
                    <q-btn label="login" color="green-7" @click.prevent="submit"/>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
          <q-avatar v-if="authenticated" color="green-10">
            <q-img :src="url+user.pp.foto"/>
            <q-menu  transition-show="jump-up" transition-hide="jump-down" class="text-green-7">
              <q-list style="min-width: 200px">
                <q-item clickable v-close-popup>
                  <q-item-section>{{user.user.name}}</q-item-section>
                </q-item>
                <q-item clickable to="/profile" class="linkmenu" active-class="aktif">
                  <q-item-section side>
                    <q-icon name="o_admin_panel_settings" color="green"/>
                  </q-item-section>
                  <q-item-section>Profile</q-item-section>
                </q-item>

                <q-separator />
                <q-item clickable class="linkmenu" @click="logout">
                  <q-item-section side>
                    <q-icon name="o_logout" color="green"/>
                  </q-item-section>
                  <q-item-section>Logout</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-avatar>
        </q-toolbar>
      </div>
    </q-header>
    
    <div v-intersection="onIntersection"></div>
    <q-page-container class="bg-grey-2">
      <div v-if="authenticated && informasiAktif.length && $route.name === 'ruang-praktikum'" class="row justify-center q-mt-sm">
      <q-card class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8 info-rolling-card" flat bordered>
        <q-carousel
          v-model="infoSlide"
          :autoplay="7000"
          swipeable
          animated
          infinite
          height="128px"
          control-color="green-7"
          class="bg-transparent"
        >
          <q-carousel-slide
            v-for="item in informasiAktif"
            :key="item.id"
            :name="item.id"
            class="q-pa-md"
          >
            <div class="row no-wrap items-start">
              <q-avatar :color="toneInfo(item.tipe).bg" text-color="white" size="34px" class="q-mr-sm">
                <q-icon :name="toneInfo(item.tipe).icon" size="18px" />
              </q-avatar>
              <div class="full-width">
                <div class="row items-center justify-between">
                  <div class="text-subtitle2 text-weight-bold">{{ item.judul }}</div>
                  <q-badge :color="toneInfo(item.tipe).bg" text-color="white">{{ labelTipe(item.tipe) }}</q-badge>
                </div>
                <div class="text-caption q-mt-xs">{{ item.isi }}</div>
                <div class="text-caption text-grey-7 q-mt-xs" v-if="item.mulai_at || item.selesai_at">
                  Berlaku: {{ formatPeriode(item) }}
                </div>
              </div>
            </div>
          </q-carousel-slide>
        </q-carousel>
      </q-card>
      </div>
      <router-view v-slot="{ Component, route }">
        <transition name="fade" mode="out-in">
          <component :is="Component" :key="route.fullPath" />
        </transition>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script>
import { computed, ref } from 'vue'
import axios from 'axios';
import { mapGetters,mapState,mapActions } from 'vuex';

export default {
  name: 'LayoutDefault',
  components: {
   
  },

  setup () {
    const visible = ref(false)
    return {
      leftDrawerOpen: ref(false),
      username: ref(""),
      password: ref(""),
      informasiAktif: ref([]),
      infoSlide: ref(null),
      visible,
      visibleClass: computed(
        () => `${visible.value ? 'bg-transparent' : 'bg-white'}`
      ),
      onIntersection (entry) {
        visible.value = entry.isIntersecting
      }
    }
      
  },
  computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol", ["url"]),
  },
  methods:{
    submit() {
      const form=new FormData
      form.append("username", this.username)
      form.append("password", this.password)
      this.signIn(form).then(() => {
        this.$router.replace({
          name: "ruang-praktikum",
        });
      }).catch((e)=>{
         this.$toast.error(`Gagal Login, Cek Username dan Password`,{
            position: "top",
            duration:2000,
            dismissible:true
         });
           return e
      })
    },
    ...mapActions({
      signIn: "auth/signIn",
    }),
    ...mapActions({
      attempt: "auth/attempt",
      logoutAction: "auth/logout",
    }),
    logout() {
      // Clear cookie
      document.cookie = "token=; path=/; domain=localhost; max-age=0";
      this.logoutAction().then(() => {
        this.$toast.success('Berhasil logout', {
          position: 'top',
          duration: 2000,
        });
        // Redirect ke frontend login page
        setTimeout(() => {
          window.location.href = 'http://localhost:8080/login';
        }, 500);
      });
    },
    async getInformasiAktif() {
      if (!this.authenticated) {
        this.informasiAktif = []
        return
      }

      await axios.get('informasi-terkini/aktif').then((response) => {
        this.informasiAktif = response.data || []
        this.infoSlide = this.informasiAktif.length ? this.informasiAktif[0].id : null
      }).catch(() => {
        this.informasiAktif = []
        this.infoSlide = null
      })
    },
    labelTipe(tipe) {
      if (tipe === 'penutupan_lab') return 'PENUTUPAN LAB'
      if (tipe === 'peringatan') return 'PERINGATAN'
      return 'INFO'
    },
    toneInfo(tipe) {
      if (tipe === 'penutupan_lab') return { bg: 'red-8', icon: 'o_campaign' }
      if (tipe === 'peringatan') return { bg: 'orange-8', icon: 'o_warning' }
      return { bg: 'green-7', icon: 'o_info' }
    },
    formatPeriode(item) {
      const fmt = (value) => {
        if (!value) return '-'
        const date = new Date(String(value).replace(' ', 'T'))
        if (isNaN(date.getTime())) return '-'
        const d = String(date.getDate()).padStart(2, '0')
        const m = String(date.getMonth() + 1).padStart(2, '0')
        const y = date.getFullYear()
        const h = String(date.getHours()).padStart(2, '0')
        const i = String(date.getMinutes()).padStart(2, '0')
        return `${d}-${m}-${y} ${h}:${i}`
      }

      return `${fmt(item.mulai_at)} s/d ${fmt(item.selesai_at)}`
    },
  },
  created(){
    // Helper to get cookie
    const getCookie = (name) => {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    }
    
    const token = getCookie('token');
    
    if (token && !this.authenticated) {
       this.attempt(token).then(() => {
            // Redirect to appropriate page
            this.$router.replace({ name: 'ruang-praktikum' });
            this.getInformasiAktif()
       });
    }
    this.getInformasiAktif()
  },
watch:{
  visible(){
    console.log(this.visible)
  },
  authenticated(){
    this.getInformasiAktif()
  }
}
}
</script>
<style lang="sass" scoped>
page 
  position: absolute
  top: 30px
  --animate-duration: 0.5s

.linkmenu:hover
  background-color: #E0F7FA
  padding-left: 20px
.my-menu-link
  border-left: 4px solid #4CAF50
  background-color: #E8F5E9
  color: #1B5E20
.aktif
  background-color: #E8F5E9
  color: #1B5E20

.info-rolling-card
  border: 1px solid #c8e6c9
  background: linear-gradient(180deg, #f5fff6 0%, #ffffff 100%)

</style>

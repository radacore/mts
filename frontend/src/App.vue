<template>
  <q-layout view="hHh LpR fFf">
    <q-header class="bg-white text-green-7 shadow z-max">
      <div v-if="isLandingTickerVisible" class="landing-ticker-bar">
        <div class="landing-ticker-label">Informasi Terkini</div>
        <div class="landing-ticker-track">
          <div class="landing-ticker-content">
            <span class="ticker-item" v-for="item in informasiAktif" :key="`ticker-${item.id}`">
              <q-badge :color="toneInfo(item.tipe).bg" text-color="white" class="q-mr-sm">
                {{ labelTipe(item.tipe) }}
              </q-badge>
              <span>{{ tickerMainText(item) }}</span>
              <span v-if="formatPeriodeTicker(item)" class="q-ml-xs text-grey-8">({{ formatPeriodeTicker(item) }})</span>
              <span class="q-mx-md text-green-9">•</span>
            </span>
          </div>
        </div>
      </div>
      <q-toolbar>
        <q-btn
          v-if="authenticated && !isLandingStaffHome"
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
          icon="o_scatter_plot"
        />

        <q-toolbar-title>
          <img src="./assets/eipa1.png" style="max-width:300px" class="gt-xs"/>
         
        </q-toolbar-title>

        <q-space/>
        <q-btn v-if="authenticated" flat round dense icon="o_notifications" class="q-mx-sm">
          <q-badge v-if="notifUnread>0" color="red" floating>{{ notifUnread }}</q-badge>
          <q-menu content-class="notif-menu-layer" transition-show="jump-down" transition-hide="jump-up" style="min-width:340px;max-width:90vw;">
            <q-list separator>
              <q-item>
                <q-item-section>
                  <q-item-label class="text-weight-bold">Notifikasi</q-item-label>
                  <q-item-label caption>{{ notifUnread }} belum dibaca</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-btn flat dense size="sm" color="green-7" label="Tandai semua" @click="tandaiSemuaDibaca" />
                </q-item-section>
              </q-item>

              <q-item v-if="notifLoading">
                <q-item-section>
                  <q-item-label>Memuat notifikasi...</q-item-label>
                </q-item-section>
              </q-item>

              <q-item v-else-if="!notifikasi.length">
                <q-item-section>
                  <q-item-label>Tidak ada notifikasi</q-item-label>
                </q-item-section>
              </q-item>

              <q-item
                v-for="item in notifikasi"
                :key="item.id"
                clickable
                @click="bukaNotifikasi(item)"
                :class="item.dibaca ? '' : 'bg-green-1'"
              >
                <q-item-section avatar>
                  <q-icon :name="item.dibaca ? 'o_mark_email_read' : 'o_mark_email_unread'" color="green-7" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ item.judul }}</q-item-label>
                  <q-item-label caption lines="2">{{ item.pesan }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
        <q-btn
          v-if="authenticated && isLandingStaffHome"
          label="Dashboard"
          icon="o_dashboard_customize"
          color="green-7"
          to="/dashboard"
          rounded
          flat
        />
        <q-btn v-if="!authenticated && $route.name !== 'login'" label="Login" icon="o_login" color="green-7" to="/login" rounded flat/>
        <q-avatar v-if="authenticated" color="green-10">
          <q-img :src="url+user.pp.foto"/>
          <q-menu  transition-show="jump-up" transition-hide="jump-down" class="text-green-7">
            <q-list style="min-width: 200px">
              <q-item clickable to="/profile" class="linkmenu" active-class="aktif">
                <q-item-section side>
                  <q-icon name="o_admin_panel_settings" color="green"/>
                </q-item-section>
                <q-item-section>Profile</q-item-section>
              </q-item>
              <q-item clickable v-close-popup class="linkmenu">
                <q-item-section side>
                  <q-icon name="o_folder_open" color="green"/>
                </q-item-section>
                <q-item-section>My Drive</q-item-section>
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
    </q-header>

    <q-drawer
      v-if="authenticated && !isLandingStaffHome"
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-white text-green-7"
      :width="280"
    >
      <q-list>
        <q-item-label header>{{user.user.name}}</q-item-label>
        <q-item clickable to="/dashboard" active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_dashboard_customize" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
            <q-item-label caption>{{user.role.role}}</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user.user.role_id==3" clickable to="/pinjam-lab" active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_science" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Peminjaman Lab</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user.user.role_id==3" clickable to="/pinjam-alat" active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_biotech" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Peminjaman Alat</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user.user.role_id==3" clickable to="/pinjam-lain" active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_local_library" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Peminjaman Lainnya</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user.user.role_id==3" clickable to="/praktikum" active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_cast_for_education" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Ruang Praktikum</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  clickable to="/inventaris"  active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_inventory" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Inventaris</q-item-label>
            <q-item-label caption>Alat dan Bahan</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  clickable to="/katalog"  active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_dvr" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Katalog Praktikum</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  clickable to="/rombel"  active-class="my-menu-link" class="linkmenu">
          <q-item-section avatar>
            <q-icon name="o_meeting_room" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Ruang Belajar</q-item-label>
            <q-item-label caption>Kelas Siswa</q-item-label>
          </q-item-section>
        </q-item>

        <q-expansion-item
        v-if="user.user.role_id==1 || user.user.role_id==2"
        icon="o_group_add"
        label="Users"
        class="text-body2 text-weight-regular"
      >
        <q-list class="q-pl-lg">
          <q-item v-if="user.user.role_id==1" to="/user/super" active-class="my-menu-link" class="linkmenu">
            <q-item-section avatar>
              <q-icon name="o_supervised_user_circle"/>
            </q-item-section>
            <q-item-section>
              <q-item-label>Super User</q-item-label>
            </q-item-section>
          </q-item>
          <q-item v-if="user.user.role_id==1 || user.user.role_id==2" to="/user/guru"  active-class="my-menu-link" class="linkmenu">
            <q-item-section avatar>
              <q-icon name="o_cast_for_education"/>
            </q-item-section>
            <q-item-section>
              <q-item-label>Guru</q-item-label>
            </q-item-section>
          </q-item>
          <q-item v-if="user.user.role_id==1"  active-class="my-menu-link" class="linkmenu" to="/user/role">
            <q-item-section avatar>
              <q-icon name="o_brightness_auto"/>
            </q-item-section>
            <q-item-section>
              <q-item-label>Level User</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-expansion-item>
      <q-expansion-item
      v-if="user.user.role_id==2"
      icon="o_event_note"
      label="Peminjaman"
      class="text-body2 text-weight-regular"
    >
    <q-list class="q-pl-lg">
      <q-item v-if="user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/pinjam-lab">
        <q-item-section avatar>
          <q-icon name="o_biotech"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Penggunaan Lab</q-item-label>
        </q-item-section>
      </q-item>
      <q-item v-if="user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/pinjam-alat">
        <q-item-section avatar>
          <q-icon name="o_history_edu"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Peminjaman Alat</q-item-label>
        </q-item-section>
      </q-item>
      <q-item v-if="user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/pinjam-lain">
        <q-item-section avatar>
          <q-icon name="o_alt_route"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Kegiatan Lain</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
    </q-expansion-item>
    <q-item v-if="user.user.role_id==2"  clickable to="/modul-lkpd"  active-class="my-menu-link" class="linkmenu">
      <q-item-section avatar>
        <q-icon name="o_collections_bookmark" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Modul Ajar & LKPD</q-item-label>
        <q-item-label caption>Archive</q-item-label>
      </q-item-section>
    </q-item>
    <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  clickable to="/data-siswa"  active-class="my-menu-link" class="linkmenu">
      <q-item-section avatar>
        <q-icon name="o_school" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Master Siswa</q-item-label>
      </q-item-section>
    </q-item>
    <q-item v-if="user.user.role_id==1 || user.user.role_id==2" clickable to="/informasi-terkini" active-class="my-menu-link" class="linkmenu">
      <q-item-section avatar>
        <q-icon name="o_campaign" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Informasi Terkini</q-item-label>
      </q-item-section>
    </q-item>
    <q-item v-if="user.user.role_id==1" clickable to="/slide" active-class="my-menu-link" class="linkmenu">
      <q-item-section avatar>
        <q-icon name="o_pets" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Slide Informasi</q-item-label>
      </q-item-section>
    </q-item>
    <q-item v-if="user.user.role_id==1" clickable to="/setelan/lokasi" active-class="my-menu-link" class="linkmenu">
      <q-item-section avatar>
        <q-icon name="o_location_on" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Pengaturan Lokasi</q-item-label>
      </q-item-section>
    </q-item>
  </q-list>
    </q-drawer>

    <q-page-container>
      <q-card
        v-if="authenticated && (user.user.role_id==3 || user.user.role_id==4) && informasiAktif.length && !isLandingStaffHome"
        class="q-ma-sm info-rolling-card"
        flat
        bordered
      >
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
                  <div class="text-subtitle2 text-weight-bold info-rolling-title">{{ item.judul }}</div>
                  <q-badge :color="toneInfo(item.tipe).bg" text-color="white">{{ labelTipe(item.tipe) }}</q-badge>
                </div>
                <div class="text-caption q-mt-xs info-rolling-body">{{ item.isi }}</div>
                <div class="text-caption text-grey-7 q-mt-xs info-rolling-meta" v-if="item.mulai_at || item.selesai_at">
                  Berlaku: {{ formatPeriode(item) }}
                </div>
              </div>
            </div>
          </q-carousel-slide>
        </q-carousel>
      </q-card>
      <router-view v-slot="{ Component, route }">
        <transition
          name="fade"
          mode="out-in"
          :enter-active-class="route.meta.enterClass"
          :leave-active-class="route.meta.leaveClass"
          class="page"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios';
import { mapGetters,mapState,mapActions } from 'vuex';

export default {
  name: 'LayoutDefault',

  components: {
   
  },

  setup () {
    return {
      leftDrawerOpen: ref(false),
      informasiAktif: ref([]),
      infoSlide: ref(null),
      notifikasi: ref([]),
      notifUnread: ref(0),
      notifLoading: ref(false),
      notifTimer: null,
    }
  },
  computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol", ["url"]),
    isLandingStaffHome() {
      const roleId = this.user && this.user.user ? this.user.user.role_id : null
      return this.authenticated && this.$route.path === '/' && [1, 2, 3].includes(roleId)
    },
    isLandingTickerVisible() {
      return this.$route.path === '/' && this.informasiAktif.length > 0
    },
  },
  methods:{
    ...mapActions({
      logoutAction: "auth/logout",
    }),
    logout() {
      this.logoutAction().then(() => {
        this.$toast.success('Berhasil logout', {
          position: 'top',
          duration: 2000,
        });
        this.$router.replace({
          name: "login",
        });
      });
    },
    async getInformasiAktif() {
      await axios.get('informasi-terkini/aktif').then((response) => {
        this.informasiAktif = response.data || []
        this.infoSlide = this.informasiAktif.length ? this.informasiAktif[0].id : null
      }).catch(() => {
        this.informasiAktif = []
        this.infoSlide = null
      })
    },
    async getNotifikasi() {
      if (!this.authenticated) {
        this.notifikasi = []
        this.notifUnread = 0
        return
      }

      this.notifLoading = true
      await axios.get('notifikasi').then((response) => {
        this.notifikasi = response.data.items || []
        this.notifUnread = response.data.unread || 0
      }).catch(() => {
        this.notifikasi = []
        this.notifUnread = 0
      }).finally(() => {
        this.notifLoading = false
      })
    },
    async bukaNotifikasi(item) {
      if (!item.dibaca) {
        await axios.put(`notifikasi/${item.id}/dibaca`)
      }

      this.getNotifikasi()

      if (item.tautan) {
        this.$router.push(item.tautan)
      }
    },
    async tandaiSemuaDibaca() {
      await axios.put('notifikasi/dibaca-semua').then(() => {
        this.getNotifikasi()
      })
    },
    startNotifPolling() {
      if (this.notifTimer) clearInterval(this.notifTimer)
      this.notifTimer = setInterval(() => {
        this.getNotifikasi()
      }, 15000)
    },
    stopNotifPolling() {
      if (this.notifTimer) {
        clearInterval(this.notifTimer)
        this.notifTimer = null
      }
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
    tickerMainText(item) {
      const judul = (item.judul || '').trim()
      const isi = (item.isi || '').trim()
      if (judul && isi) return `${judul}: ${isi}`
      return judul || isi || '-'
    },
    formatTanggalTicker(value) {
      if (!value) return ''
      const date = new Date(String(value).replace(' ', 'T'))
      if (isNaN(date.getTime())) return ''
      const d = String(date.getDate()).padStart(2, '0')
      const m = String(date.getMonth() + 1).padStart(2, '0')
      const y = date.getFullYear()
      return `${d}-${m}-${y}`
    },
    formatPeriodeTicker(item) {
      const mulai = this.formatTanggalTicker(item.mulai_at)
      const selesai = this.formatTanggalTicker(item.selesai_at)
      if (mulai && selesai) return `${mulai} s/d ${selesai}`
      return mulai || selesai || ''
    },
  },
  created(){
    this.getInformasiAktif()
    this.getNotifikasi()
    this.startNotifPolling()
  },
  beforeUnmount() {
    this.stopNotifPolling()
  },
  watch: {
    authenticated() {
      this.getInformasiAktif()
      this.getNotifikasi()
      if (this.authenticated) {
        this.startNotifPolling()
      } else {
        this.stopNotifPolling()
      }
    },
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

.info-rolling-title
  font-size: 1.02rem

.info-rolling-body
  font-size: 0.92rem
  line-height: 1.35

.info-rolling-meta
  font-size: 0.84rem

.landing-ticker-bar
  display: flex
  align-items: center
  border-bottom: 1px solid #e0efe3
  background: linear-gradient(90deg, #f5fff6 0%, #edf9ef 100%)
  padding: 0 8px

.landing-ticker-label
  flex: 0 0 auto
  color: #ffffff
  background: #2e7d32
  font-size: 0.78rem
  font-weight: 700
  text-transform: uppercase
  letter-spacing: 0.03em
  margin-right: 10px
  padding: 7px 12px

.landing-ticker-track
  flex: 1
  overflow: hidden
  white-space: nowrap
  min-width: 0

.landing-ticker-content
  display: inline-block
  padding: 6px 0
  color: #1b5e20
  font-size: 0.86rem
  padding-left: 100%
  animation: ticker-right-to-left 26s linear infinite

.ticker-item
  display: inline-flex
  align-items: center

@keyframes ticker-right-to-left
  0%
    transform: translateX(0)
  100%
    transform: translateX(-100%)

:deep(.notif-menu-layer)
  z-index: 100000 !important

</style>

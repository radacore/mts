<template>
  <q-layout view="hHh LpR fFf">
    <q-header class="bg-white text-green-7 shadow z-max">
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
      v-if="user.user.role_id==1 || user.user.role_id==2"
      icon="o_event_note"
      label="Peminjaman"
      class="text-body2 text-weight-regular"
    >
    <q-list class="q-pl-lg">
      <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/pinjam-lab">
        <q-item-section avatar>
          <q-icon name="o_biotech"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Penggunaan Lab</q-item-label>
        </q-item-section>
      </q-item>
      <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/pinjam-alat">
        <q-item-section avatar>
          <q-icon name="o_history_edu"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Peminjaman Alat</q-item-label>
        </q-item-section>
      </q-item>
      <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/pinjam-lain">
        <q-item-section avatar>
          <q-icon name="o_alt_route"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Kegiatan Lain</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
    </q-expansion-item>
    <q-item v-if="user.user.role_id==1 || user.user.role_id==2"  clickable to="/modul-lkpd"  active-class="my-menu-link" class="linkmenu">
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
        v-if="showGuruTataTertib"
        class="q-ma-sm tata-tertib-card"
        flat
        bordered
      >
        <q-card-section class="q-pa-md q-pa-lg-sm tata-tertib-content">
          <div class="text-h5 text-center text-weight-bold text-green-10">TATA TERTIB</div>
          <div class="text-h6 text-center text-weight-bold text-green-9 q-mt-xs">LABORATORIUM IPA TERPADU</div>
          <div class="text-body1 q-mt-md text-justify">
            Sebelum memasuki laboratorium atau ruang praktikum, peserta didik harus mematuhi tata tertib
            laboratorium Digital IPA Terpadu. Adapun tata tertib yang harus dipatuhi sebagai berikut:
          </div>
          <ol class="tata-tertib-list q-mt-sm">
            <li>Peserta didik hadir 10 menit sebelum praktikum dimulai.</li>
            <li>Peserta didik wajib memakai perlindungan diri (jas laboratorium, masker, kacamata pelindung, dan sarung tangan).</li>
            <li>Alat/bahan praktikum harus digunakan sesuai petunjuk penggunaan dan anjuran guru.</li>
            <li>Dilarang makan/minum dan membawa makanan/minuman ke ruangan laboratorium kecuali untuk kegiatan praktikum.</li>
            <li>Peserta didik tidak diperkenankan menghirup dan menggunakan zat kimia berbahaya.</li>
            <li>Peserta didik memperhatikan label zat kimia berbahaya dan jika label rusak/hilang segera dilaporkan kepada guru/laboran.</li>
            <li>Peserta didik tidak diperkenankan mencampuradukkan zat kimia berbahaya dan mereaksikan suatu zat dengan zat lain tanpa petunjuk guru/laboran.</li>
            <li>Peserta didik bertanggung jawab atas keamanan dan kebersihan alat dan bahan baik saat praktikum maupun setelah praktikum.</li>
            <li>Peserta didik harus mengganti alat praktikum yang dirusakkan/dipecahkan.</li>
            <li>Menjaga ketenangan dan ketertiban selama berada di ruangan laboratorium.</li>
            <li>Tidak diperkenankan membawa tas, jaket, topi, atau barang yang tidak ada kaitannya dengan praktikum ke ruangan laboratorium.</li>
            <li>Tidak diperkenankan membawa keluar alat/bahan praktikum tanpa seizin guru/laboran.</li>
            <li>Membuang sampah pada tempatnya.</li>
            <li>Peserta didik menjaga kebersihan ruangan laboratorium.</li>
          </ol>
        </q-card-section>
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
    showGuruTataTertib() {
      const roleId = this.user && this.user.user ? this.user.user.role_id : null
      return this.authenticated && [2, 3].includes(roleId) && this.$route.path === '/dashboard'
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
  },
  created(){
    this.getNotifikasi()
    this.startNotifPolling()
  },
  beforeUnmount() {
    this.stopNotifPolling()
  },
  watch: {
    authenticated() {
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

.tata-tertib-card
  border: 1px solid #c8e6c9
  background: linear-gradient(180deg, #f1fff2 0%, #ffffff 100%)

.tata-tertib-content
  color: #1b3f1f

.tata-tertib-list
  margin: 0
  padding-left: 22px
  line-height: 1.65
  column-count: 2
  column-gap: 28px

.tata-tertib-list li
  margin-bottom: 6px
  break-inside: avoid

@media (max-width: 900px)
  .tata-tertib-list
    column-count: 1

:deep(.notif-menu-layer)
  z-index: 100000 !important

</style>

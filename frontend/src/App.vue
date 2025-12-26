<template>
  <q-layout view="hHh LpR fFf">
    <q-header class="bg-white text-green-7 shadow">
      <q-toolbar>
        <q-btn
          v-if="authenticated"
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
        <q-icon v-if="authenticated" name="o_mail" size="sm" class="q-mx-sm"/>
        <q-icon v-if="authenticated" name="o_notifications" size="sm" class="q-mx-sm"/>
        <q-btn v-if="!authenticated" label="Login" icon="o_login" color="green-7" to="/login" rounded flat/>
        <q-btn v-if="!authenticated" label="Home" icon="o_home" color="green-7" to="/" flat rounded/>
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
      v-if="authenticated"
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-white text-green-7"
      :width="280"
    >
      <q-list>
        <q-item-label header>{{user.user.name}}</q-item-label>
        <q-item clickable to="/" active-class="my-menu-link" class="linkmenu">
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
          <q-item v-if="user.user.role_id==1 || user.user.role_id==2" to="/user/siswa"  active-class="my-menu-link" class="linkmenu">
            <q-item-section avatar>
              <q-icon name="o_school"/>
            </q-item-section>
            <q-item-section>
              <q-item-label>Siswa</q-item-label>
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
      <q-item v-if="user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/peminjaman/lab">
        <q-item-section avatar>
          <q-icon name="o_biotech"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Penggunaan Lab</q-item-label>
        </q-item-section>
      </q-item>
      <q-item v-if="user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/peminjaman/alat">
        <q-item-section avatar>
          <q-icon name="o_history_edu"/>
        </q-item-section>
        <q-item-section>
          <q-item-label>Peminjaman Alat</q-item-label>
        </q-item-section>
      </q-item>
      <q-item v-if="user.user.role_id==2"  active-class="my-menu-link" class="linkmenu" to="/peminjaman/lainnya">
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
    <q-item v-if="user.user.role_id==2"  clickable to="/data-siswa"  active-class="my-menu-link" class="linkmenu">
      <q-item-section avatar>
        <q-icon name="o_school" />
      </q-item-section>
      <q-item-section>
        <q-item-label>Master Siswa</q-item-label>
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
  </q-list>
    </q-drawer>

    <q-page-container>
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
import { mapGetters,mapState,mapActions } from 'vuex';

export default {
  name: 'LayoutDefault',

  components: {
   
  },

  setup () {
    return {
      leftDrawerOpen: ref(false)
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
    ...mapActions({
      logoutAction: "auth/logout",
    }),
    logout() {
      this.logoutAction().then(() => {
        this.$router.replace({
          name: "login",
        });
      });
    },
  },
  created(){

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

</style>


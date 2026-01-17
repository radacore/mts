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
          <q-btn v-if="!authenticated" label="Login" icon="o_login" color="green-7" rounded flat href="http://localhost:8081/login" />
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
      </div>
    </q-header>
    
    <div v-intersection="onIntersection"></div>
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
import { computed, ref } from 'vue'
import { mapGetters,mapState,mapActions } from 'vuex';

export default {
  name: 'LayoutDefault',
  components: {
   
  },

  setup () {
    const visible = ref(false)
    return {
      leftDrawerOpen: ref(false),
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
    ...mapActions({
      attempt: "auth/attempt",
      logoutAction: "auth/logout",
    }),
    logout() {
      // Clear cookie
      document.cookie = "token=; path=/; domain=localhost; max-age=0";
      this.logoutAction().then(() => {
        this.$router.replace({
          name: "home",
        });
      });
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
       });
    }
  },
watch:{
  visible(){
    console.log(this.visible)
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

</style>


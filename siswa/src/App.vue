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
      <div v-if="showSiswaTataTertib" class="row justify-center q-mt-sm">
      <q-card class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8 tata-tertib-card" flat bordered>
        <q-card-section class="q-pa-md tata-tertib-content">
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
    showSiswaTataTertib() {
      return this.authenticated && this.$route.name === 'ruang-praktikum'
    },
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
      document.cookie = 'token=; path=/; max-age=0';
      this.logoutAction().then(() => {
        this.$toast.success('Berhasil logout', {
          position: 'top',
          duration: 2000,
        });
        const { protocol, hostname } = window.location;
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1';
        const target = isLocal
          ? `${protocol}//${hostname}:8080/`
          : `${protocol}//${hostname}/`;
        window.location.href = target;
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

</style>

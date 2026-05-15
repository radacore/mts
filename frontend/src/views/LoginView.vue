``<template>
  <q-page class="flex flex-center">
    <q-card style="width:400px" class="shadow-10 q-pa-md" bordered>
        <q-card-section>
          <div>
            <img src="../assets/eipa2.png" style="max-width:200px"/>
          </div>
            <div>
                <span class="text-h6 text-green">Login</span>
            </div>
            <div class="text-caption text-grey">
                <span>Welcome to E-ipa! Log in to your account</span>
            </div>
        </q-card-section>
        <q-card-section>
            <q-input outlined v-model="username" label="username" class="q-my-md" color="green-7">
                <template v-slot:prepend>
                  <q-icon name="ion-person" color="green-7" />
                </template>
              </q-input>
            <q-input outlined v-model="password" type="password" label="password" class="q-my-md" color="green-7">
                <template v-slot:prepend>
                  <q-icon name="ion-key" color="green-7" />
                </template>
              </q-input>
        </q-card-section>
        <q-card-actions align="right">
            <q-btn label="Login" class="q-mr-md" style="width:100px" color="green-7" @click="submit"/>
        </q-card-actions>
           
       </q-card>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity'
import { mapActions } from 'vuex'
export default {
setup(){
    return{
        username:ref(""),
        password:ref(""),
    }
},
methods:{
    submit() {
      const form=new FormData
      form.append("username", this.username)
      form.append("password", this.password)
      this.signIn(form).then(() => {
        // Cek role_id setelah login berhasil
        const user = this.$store.getters['auth/user'];
        
        if (user && user.user && user.user.role_id === 4) {
          // Jika siswa (role_id=4), redirect ke siswa app dengan token
          const token = this.$store.state.auth.token;
          // Logout dari frontend (bersihkan state)
          this.$store.commit('auth/SET_TOKEN', null);
          this.$store.commit('auth/SET_USER', null);
          // Redirect ke siswa app
          window.location.href = `http://localhost:8082/auto-login?token=${token}`;
        } else {
          // Role lainnya tetap di frontend
          this.$router.replace({
            name: "home", // Sekarang mengarah ke /dashboard
          });
        }
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
}
}
</script>

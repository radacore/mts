``<template>
  <q-page class="flex flex-center">
    <q-card style="width:400px; height:500px" flat>
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
            <q-btn label="Sig IN" class="q-mr-md" style="width:100px" color="green-7" @click="submit"/>
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
        // Get token from store state instead of response
        const token = this.$store.state.auth.token;
        // console.log("Login success, token:", token);

        if (token) {
             document.cookie = `token=${token}; path=/; domain=localhost; max-age=86400`;
        }

        const user = this.$store.getters['auth/user'];
        // console.log("User role:", user?.role?.id);

        if (user && user.role && user.role.id === 4) {
             window.location.href = `http://localhost:8082`;
        } else {
            this.$router.replace({
                name: "home",
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


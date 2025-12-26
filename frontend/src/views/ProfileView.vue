<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated" class="row">
        <div class="col-12 col-sm-4 col-lg-4 co-md-4 col-xl-4">
            <q-card class="q-mr-sm">
                <div class="text-center q-pt-md">
                    <q-avatar size="120px" color="green-7">
                        <img :src="url+fotoku"/>
                    </q-avatar>
                    <q-btn @click="show=!show" round icon="o_photo_camera" color="primary" class="absolute-center" style="margin-left:40px" size="sm"/>
                </div>
                <q-card-section>
                    <div class="text-center">
                        <span class="text-h6">{{name}}</span>
                        <p>{{email}}</p>
                        <q-btn round icon="edit_square" flat color="green-7" size="sm" class="absolute-center" style="margin-top:30px"/>
                    </div>
                </q-card-section>
            </q-card>
            <q-card v-show="show" class="q-mr-sm q-mt-sm">
                <q-card-section>
                    <q-file outlined bottom-slots v-model="foto" label="pilih foto" counter>
                        <template v-slot:prepend>
                          <q-icon name="fas fa-camera-retro" @click.stop.prevent />
                        </template>
                        <template v-slot:append>
                          <q-icon name="close" @click.stop.prevent="foto = null" class="cursor-pointer" />
                        </template>
                
                        <template v-slot:hint>
                          *.jpg,.png,.jpeg
                        </template>
                      </q-file>
                </q-card-section>
                <q-card-actions align="center">
                    <q-btn @click="upload" label="upload" color="green-7" style="width:100px"/>
                </q-card-actions>
            </q-card>
        </div>
        <div class="col-12 col-sm-8 col-lg-8 co-md-8 col-xl-8">
            <q-card>
                <q-card-section>
                    <span class="text-h6">Profil {{name}}</span>
                    <q-separator/>
                    <q-form>
                        <q-input v-model="name" label="Nama Lengkap" dense />
                        <q-input v-model="email" label="E-mail" dense />
                        <q-input v-model="username" label="Username" dense />
                        <q-input v-model="password" label="Password" type="password" dense />
                    </q-form>
                </q-card-section>
                <q-card-actions>
                    <q-btn @click="update" label="Update" color="green-7" no-caps style="width:100px"/>
                </q-card-actions>
            </q-card>
            <bio-data/>
        </div>
    </div>
  </q-page>
</template>

<script>
import axios from 'axios';
import { mapGetters,mapState } from 'vuex';
import { ref } from '@vue/reactivity';
import BioData from '@/components/BioData.vue';
export default {
components:{
BioData,
},
setup(){
    return{
        id:ref(""),
        name:ref(""),
        email:ref(""),
        username:ref(""),
        password:ref(""),
        foto:ref(null),
        fotoku:ref(null),
        show:ref(false)
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
    async getProfile(){
        await axios.get("profiles").then((response)=>{
            this.id=response.data.data.id
            this.name=response.data.data.name
            this.email=response.data.data.email
            this.username=response.data.data.username
            this.fotoku=response.data.foto.foto
        })
    },
    async update(){
        const form=new FormData
        form.append("id",this.id)
        form.append("name",this.name)
        form.append("email",this.email)
        form.append("username",this.username)
        form.append("password",this.password)
        await axios.post("profiles/update",form).then((response)=>{
            this.password=""
            this.$toast.success(`berhasil di Update`)
            this.getProfile()
            return response
        }).catch((error)=>{
            this.$toast.error(`Gagal Mengupdate, Mohon Cek kebali`,{
            position: "top",
            duration:2000,
            dismissible:true
         });
            return error
        })
    },
    async upload(){
        const form=new FormData
        form.append("foto", this.foto)
    await axios.post("profiles", form).then((response)=>{
            this.foto=null
            this.show=false
            this.$toast.success(`berhasil diupload`)
            this.getProfile()
            return response
        }).catch((error)=>{
            this.$toast.error(`Gagal Mengupload, Mohon Cek kebali`,{
            position: "top",
            duration:2000,
            dismissible:true
         });
            return error
        })
    }
},
created(){
this.getProfile()
}
}
</script>

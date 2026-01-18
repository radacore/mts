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
                    <q-form class="q-mt-md">
                        <div class="row q-col-gutter-sm">
                            <div class="col-12 col-md-6">
                                <q-input v-model="name" label="Nama Lengkap" dense outlined readonly bg-color="grey-2" hint="Hubungi admin untuk mengubah nama" />
                            </div>
                            <div class="col-12 col-md-6">
                                <q-input v-model="email" label="E-mail" dense outlined readonly bg-color="grey-2" hint="Hubungi admin untuk mengubah email" />
                            </div>
                            <div class="col-12">
                                <q-input v-model="username" label="Username (NIS)" dense outlined readonly bg-color="grey-2" />
                            </div>
                        </div>

                        <q-separator class="q-my-md" />
                        <div class="text-subtitle2 q-mb-sm">Ganti Password</div>
                        
                        <q-input v-model="old_password" label="Password Lama" type="password" dense outlined :rules="[val => !password || !!val || 'Password lama harus diisi jika ingin mengganti']" />
                        <q-input v-model="password" label="Password Baru" type="password" dense outlined hint="Kosongkan jika tidak ingin mengganti" />
                        <q-input v-model="password_confirmation" label="Konfirmasi Password Baru" type="password" dense outlined :rules="[val => val === password || 'Password tidak sama']" />

                    </q-form>
                </q-card-section>
                <q-card-actions>
                    <q-btn @click="update" label="Simpan Perubahan" color="green-7" no-caps icon="save" :disable="loading"/>
                </q-card-actions>
            </q-card>
        </div>
    </div>
  </q-page>
</template>

<script>
import axios from 'axios';
import { mapGetters,mapState } from 'vuex';
import { ref } from '@vue/reactivity';

export default {
setup(){
    return{
        id:ref(""),
        name:ref(""),
        email:ref(""),
        username:ref(""),
        password:ref(""),
        old_password:ref(""),
        password_confirmation:ref(""),
        foto:ref(null),
        fotoku:ref(null),
        show:ref(false),
        createdAt: ref(''),
        updatedAt: ref(''),
        loading: ref(false)
    }
},
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol", ["url"]),
    isNewUser() {
        if (!this.createdAt || !this.updatedAt) return false;
        return this.createdAt === this.updatedAt;
    }
},
methods:{
    async getProfile(){
        await axios.get("profiles").then((response)=>{
            this.id=response.data.data.id
            this.name=response.data.data.name
            this.email=response.data.data.email
            this.username=response.data.data.username
            this.fotoku=response.data.foto.foto
            
            // Set timestamp untuk deteksi user baru
            this.createdAt = response.data.data.created_at;
            this.updatedAt = response.data.data.updated_at;
        })
    },
    async update(){
        if(this.password && this.password !== this.password_confirmation){
             this.$toast.error('Konfirmasi password tidak sesuai');
             return;
        }

        const form=new FormData
        form.append("id",this.id)
        // Kita tidak perlu update name/email/username dari sini jika readonly, 
        // tapi backend userController masih fleksibel menerima jika ada.
        // Untuk amannya, kita kirimkan saja value yg ada (readonly).
        form.append("name",this.name)
        form.append("email",this.email)
        form.append("username",this.username)
        
        // Password logic
        if(this.password) {
             form.append("password",this.password)
             form.append("password_confirmation",this.password_confirmation)
             form.append("old_password",this.old_password)
        }

        this.loading = true;
        await axios.post("profiles/update",form).then((response)=>{
            this.loading = false;
            this.password="";
            this.old_password="";
            this.password_confirmation="";
            
            this.$toast.success(`Profil berhasil diperbarui`)
            this.getProfile()
            
            this.updatedAt = new Date().toISOString(); 
            
            return response
        }).catch((error)=>{
            this.loading = false;
            let msg = "Gagal Mengupdate, Mohon Cek kembali";
            // Tampilkan pesan error spesifik dari backend jika ada
            if(error.response && error.response.data && error.response.data.message){
                msg = error.response.data.message;
            }
            this.$toast.error(msg,{
            position: "top",
            duration:3000,
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
            this.$toast.success(`Foto berhasil diupload`)
            this.getProfile()
            return response
        }).catch((error)=>{
            this.$toast.error(`Gagal Mengupload`);
            return error
        })
    }
},
created(){
this.getProfile()
}
}
</script>

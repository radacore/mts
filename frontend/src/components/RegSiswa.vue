<template>
  <div>
    <q-btn 
      v-if="cekin" 
      flat 
      dense 
      round 
      icon="lock_reset" 
      color="orange" 
      @click="confirmReset = true"
    >
      <q-tooltip>Reset password ke NIS</q-tooltip>
    </q-btn>
    <q-badge v-else color="grey" label="Belum Aktif" />

    <!-- Dialog Konfirmasi -->
    <q-dialog v-model="confirmReset" persistent>
      <q-card style="min-width: 300px">
        <q-card-section class="row items-center">
          <q-icon name="warning" color="orange" size="md" class="q-mr-sm" />
          <span>Reset password siswa ini ke NIS?</span>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Batal" v-close-popup />
          <q-btn flat label="Reset" color="orange" @click="resetPassword" :loading="loading" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import axios from 'axios';
import { ref } from '@vue/reactivity';

export default {
props:["email",'dataId'],
setup(){
    return{
        cekin:ref(""),
        confirmReset:ref(false),
        loading:ref(false),
    }
},
watch:{
email(){
    this.cekUser()
}
},
methods:{
    async cekUser(){
        await axios.get("cekUser/"+this.email).then((response)=>{
            this.cekin=response.data?.id || null
        })
    },
    async resetPassword(){
        this.loading = true
        await axios.post("resetPasswordSiswa", { id: this.cekin }).then((response)=>{
            this.$toast.success('Password berhasil direset ke NIS')
            this.confirmReset = false
            return response
        }).catch((error)=>{
            this.$toast.error('Gagal reset password')
            return error
        }).finally(()=>{
            this.loading = false
        })
    }
},
created(){
this.cekUser()
}
}
</script>

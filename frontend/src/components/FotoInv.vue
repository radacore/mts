<template>
    <q-btn @click="dialogUpload=true" round icon="far fa-image" color="cyan-7" size="xs" flat=""/>
    <q-dialog v-model="dialogUpload" persistent position="top">
        <q-card style="min-width: 350px">
          <q-card-section>
            <div class="text-h6">Upload Foto Alat/Bahan</div>
          </q-card-section>
  
          <q-card-section class="q-pt-none">
            <q-file filled bottom-slots v-model="foto" label="Pilih Foto" counter>
              <template v-slot:prepend>
                <q-icon name="fas fa-camera-retro" @click.stop.prevent />
              </template>
              <template v-slot:append>
                <q-icon name="close" @click.stop.prevent="model = null" class="cursor-pointer" />
              </template>
            </q-file>
          </q-card-section>
  
          <q-card-actions align="right" class="text-primary">
            <q-btn label="Cancel" color="grey" v-close-popup />
            <q-btn label="Upload" color="green-7" @click="upload" />
          </q-card-actions>
        </q-card>
      </q-dialog>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios'
export default {
props:["id"],
setup(){
    return{
        dialogUpload:ref(false),
        foto:ref(null)
    }
},
methods:{
 async upload(){
    const form=new FormData
    form.append("id",this.id)
    form.append("foto",this.foto)
  await axios.post("inventaris/foto/", form).then((response)=>{
    this.$store.commit('kontrol/SET_TRIGER')
    this.foto=null
    this.dialogUpload=false
    this.$toast.success(`upload berhasil`)
    return response
  }).catch((error)=>{
    this.$toast.error(`Gagal, Pastikan Format jpg,png atau jpeg`,{
            position: "top",
            duration:2000,
            dismissible:true
         });
    return error
  })
  }
},
created(){

}
}
</script>


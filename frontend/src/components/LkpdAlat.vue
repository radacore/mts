<template>
    <q-btn @click="modalLkpd=true" round icon="far fa-file-pdf" color="dark" size="xs" flat=""/>
    <q-dialog v-model="modalLkpd" persistent>
        <q-card style="min-width: 350px">
          <q-card-section>
            <div class="text-h6 text-green-7">UPLOAD LKPD</div>
          </q-card-section>
  
          <q-card-section class="q-pt-none">
            <q-file filled bottom-slots v-model="lkpd" label="Pilih File" counter>
                <template v-slot:prepend>
                  <q-icon name="cloud_upload" @click.stop.prevent />
                </template>
                <template v-slot:append>
                  <q-icon name="close" @click.stop.prevent="lkpd = null" class="cursor-pointer" />
                </template>
        
                <template v-slot:hint>
                  .pdf, .docx
                </template>
              </q-file>
          </q-card-section>
  
          <q-card-actions align="right" class="text-green-7">
            <q-btn flat label="Batal" v-close-popup />
            <q-btn flat label="Upload" @click="upload" />
          </q-card-actions>
        </q-card>
      </q-dialog>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios';
export default {
props:["id"],
setup(){
    return{
        modalLkpd:ref(false),
        lkpd:ref(null),
    }
},
methods:{
async upload(){
    const form=new FormData
    form.append("id", this.id)
    form.append("lkpd", this.lkpd)
    await axios.post("lkpdalat",form).then((response)=>{
        this.modalLkpd=false
        this.lkpd=null
        this.$toast.success(`LKPD Berhasil di Upload..`)
        this.$store.commit('kontrol/SET_TRIGER')
        return response
    })
}
}
}
</script>


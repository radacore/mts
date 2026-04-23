<template>
  <div>
    <q-btn
      v-if="typeof(status)=='undefined'"
      :disable="late || isClosed"
      :label="absenLabel"
      rounded
      :color="isClosed ? 'grey-7' : 'green-7'"
      @click="absen"
    />
    <q-btn v-else label="Sudah Absen" rounded color="grey" disable @click="absen"/>
  </div>
</template>

<script>
import axios from 'axios'
import { ref } from '@vue/reactivity'

export default {
props:["absen_id", "late", "isClosed"],
setup(){
    return{
        status:ref(""),
    }
},
watch:{
    absen_id(){
        this.cekAbsen();
    }
},
methods:{
    async absen(){
        const form=new FormData
        form.append("absensi_id", this.absen_id)
    await axios.post("absenSiswa", form).then((response)=>{
        this.cekAbsen()
        return response
    })
    },
    async cekAbsen(){
        await axios.get("absenSiswa/cek/"+this.absen_id).then((response)=>{
            this.status=response.data.id
            console.log(response.data.id)
        })
    }
},
computed:{
    absenLabel(){
        if (this.isClosed) return 'Absensi Ditutup'
        if (this.late) return 'Waktu Absensi Habis'
        return 'Click untuk Absen'
    }
},
created(){
    this.cekAbsen();
}
}
</script>

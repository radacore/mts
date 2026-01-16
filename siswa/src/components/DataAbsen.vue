<template>
  <div>
    <q-btn v-if="typeof(status)=='undefined'" label="Click untuk Absen" rounded color="green-7" @click="absen"/>
    <q-btn v-else  label="Sudah Absen" rounded color="grey" disable @click="absen"/>
  </div>
</template>

<script>
import axios from 'axios'
import { ref } from '@vue/reactivity'

export default {
props:["absen_id"],
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
created(){
    this.cekAbsen();
}
}
</script>


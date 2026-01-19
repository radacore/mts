<template>
  <div>
    <q-list dense>
      <q-item>
        <q-item-section>
          <q-btn label="download" flat dense rounded style="max-width:100px"  @click="downloadExcel"/>
        </q-item-section>
      </q-item>
      <q-separator/>
        <q-item v-for="row in datas" :key="row.id">
            <q-item-section avatar>
              <q-avatar color="teal" text-color="white">
                <img :src="url+row.user.foto_profile.foto"/>
              </q-avatar>
            </q-item-section>
    
            <q-item-section>
                <q-item-label>{{row.user.name}}</q-item-label>
                <q-item-label caption>Absen Tanggal {{dateTime(row.tgl_absen)}}. Pukul {{pukul(row.created_at)}} Wita</q-item-label>
            </q-item-section>
          </q-item>
    </q-list>
  </div>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios'
import { mapState } from 'vuex'
import moment from "moment";
import "moment/locale/id";
import * as XLSX from 'xlsx';

moment.locale("id");
export default {
props:["absen_id"],
setup(){
    return{
        datas:ref([]),
    }
},
watch:{
absen_id(){
    this.getAbsens();
}
},
computed:{
...mapState("kontrol",["url"])
},
methods:{
    dateTime(value) {
      return moment(value).format('LL');
    },
    pukul(value) {
      return moment(value).format('h:mm:ss');
    },
    async getAbsens(){
        await axios.get("dataAbsen/"+this.absen_id).then((response)=>{
            this.datas=response.data
        })
    },
    downloadExcel() {
       const dataToExport = this.datas.map(row => ({
          'Nama Siswa': row.user.name,
          'Tanggal Absen': this.dateTime(row.tgl_absen),
          'Jam Absen': this.pukul(row.created_at)
        }));
        
        const ws = XLSX.utils.json_to_sheet(dataToExport);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Absensi");
        XLSX.writeFile(wb, `Data_Absensi_${this.dateTime(new Date())}.xlsx`);
    },
  
},
created(){
this.getAbsens();
}
}
</script>


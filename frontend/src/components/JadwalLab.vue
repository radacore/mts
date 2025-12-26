<template>
    <q-table
    :rows="rows"
    :columns="columns"
    :loading="loading"
    row-key="name"
    flat
    dense
    class="my-sticky-header-table"
 >
 <template v-slot:loading>
    <q-inner-loading showing>
        <q-spinner-ios size="30px" color="green-7" />
    </q-inner-loading>
  </template>
 <template v-slot:top-left>
    <span class="text-green-7">Jadwal Penggunaan Lab untuk Kegiatan Praktikum</span>
  </template>
  <template v-slot:body-cell-hari="props">
    <q-td :props="props">
      {{day(props.row.tgl)}}, {{dateTime(props.row.tgl)}}
    </q-td>
  </template>
  <template v-slot:body-cell-foto="props">
    <q-td :props="props">
     <q-avatar color="green-7">
        <img :src="url+props.row.foto"/>
     </q-avatar>
    </q-td>
  </template>
  <template v-slot:body-cell-jam="props">
    <q-td :props="props">
      {{props.row.jam}} - {{props.row.jam_selesai}} Wita
    </q-td>
  </template>
  <template v-slot:body-cell-topik="props">
    <q-td :props="props">
      {{props.row.topik}}
    </q-td>
  </template>
  <template v-slot:body-cell-kelas="props">
    <q-td :props="props">
      {{props.row.kelas}}
    </q-td>
  </template>
 </q-table>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios'
import { mapState } from 'vuex';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
setup(){
    const columns = [
        { name: 'foto', align: 'left', label: '', sortable: true },
        { name: 'guru', align: 'left', label: 'Guru Mapel', field: 'peminjam', sortable: true },
        { name: 'hari', align: 'left', label: 'Hari', field: 'tgl', sortable: true },
        { name: 'jam', align: 'left', label: 'JAM', sortable: true },
        { name: 'topik', align: 'left', label: 'Topik', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', sortable: true },
    ]
    return{
        columns,
        loading:ref(false),
        rows:ref([]),
    }
},
computed:{
...mapState("kontrol",["url"])
},
methods:{
    dateTime(value) {
      return moment(value).format('ll');
    },
    day(value) {
      return moment(value).format('dddd');
    },
    async getJadwal(){
        this.loading=true
        await axios.get("jadwals").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            setTimeout(()=>{
              this.loading=false
          },1000);
        })
    }
},
created(){
    this.getJadwal();
}
}
</script>
<style lang="sass">
.my-sticky-header-table
.q-table thead tr th
    background: rgba(200, 247, 197)
</style>



<template>
  <q-table
    title="Jadwal Penggunaan Lab untuk Kegiatan Praktikum"
    :rows="tampilRows"
    :columns="columns"
    :pagination="pagination"
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
  <template v-slot:body-cell-hari="props">
    <q-td :props="props">
      {{day(props.row.tgl)}}, {{dateTime(props.row.tgl)}}
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
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
props: {
  mendatang: { type: Boolean, default: false },
  batas: { type: Number, default: 10 },
},
setup(){
    const columns = [
        { name: 'guru', align: 'left', label: 'Guru Mapel', field: 'peminjam', sortable: true },
        { name: 'hari', align: 'left', label: 'Hari', field: 'tgl', sortable: true },
        { name: 'jam', align: 'left', label: 'JAM', sortable: true },
        { name: 'topik', align: 'left', label: 'Topik', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', sortable: true },
    ]
    return{
        columns,
        pagination: ref({ rowsPerPage: 15 }),
        loading:ref(false),
        rows:ref([]),
    }
},
computed:{
    tampilRows(){
      const list = this.rows || []
      if (!this.mendatang) return list
      const hariIni = moment().format('YYYY-MM-DD')
      return list
        .filter((row) => (row.tgl || '') >= hariIni)
        .sort((a, b) => String(a.tgl || '').localeCompare(String(b.tgl || '')) || String(a.jam || '').localeCompare(String(b.jam || '')))
        .slice(0, this.batas)
    },
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

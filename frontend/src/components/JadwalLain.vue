<template>
  <div>
     <q-table
      title="Peminjaman Kegiatan Lain"
      :rows="rows"
      :columns="columns"
      :pagination="pagination"
      :loading="loading"
      row-key="name"
      dense
      flat
    >
    <template v-slot:loading>
    <q-inner-loading showing>
        <q-spinner-ios size="30px" color="green-7" />
    </q-inner-loading>
     </template>
     <template v-slot:body-cell-tgl="props">
        <q-td :props="props">
             {{day(props.row.tgl)}}, {{dateTime(props.row.tgl)}}
        </q-td>
     </template>   
     </q-table>
  </div>
</template>

<script>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
props: {
  mendatang: { type: Boolean, default: false },
  batas: { type: Number, default: 10 },
},
setup(props){
    const columns=[
         { name: 'tgl', align: 'left', label: 'TANGGAL PEMINJAMAN', field: 'calories', sortable: true },
         { name: 'mulai', align: 'left', label: 'JAM MULAI', field: 'mulai', sortable: true },
         { name: 'selesai', align: 'left', label: 'JAM SELESAI', field: 'selesai', sortable: true },
         { name: 'kegiatan', align: 'left', label: 'KEGIATAN', field: 'kegiatan', sortable: true },
    ]
    const rows=ref([])
    const loading=ref(false)
    const pagination = ref({ rowsPerPage: 15 })
    const dateTime=(value)=>{
        return moment(value).format('ll');
    }
    const day=(value)=>{
        return moment(value).format('dddd');
    }
    function getDataLain(){
        loading.value=true
        axios.get('/pinjamlain').then((response)=>{
            setTimeout(()=>{
                loading.value=false
                rows.value=response.data
            },1000)
            
            console.log(response.data)
        })
    }
    onMounted(()=>{
        getDataLain()
    })
    const tampilRows = computed(() => {
      const list = rows.value || []
      if (!props.mendatang) return list
      const hariIni = moment().format('YYYY-MM-DD')
      return list
        .filter((row) => (row.tgl || '') >= hariIni)
        .sort((a, b) => String(a.tgl || '').localeCompare(String(b.tgl || '')) || String(a.mulai || '').localeCompare(String(b.mulai || '')))
        .slice(0, props.batas)
    })
    return{
        columns,
        rows: tampilRows,
        loading,
        pagination,
        dateTime,
        day,
    }
}
}
</script>

<style>

</style>

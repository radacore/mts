<template>
  <div>
     <q-table
      title="Peminjaman Kegiatan Lain"
      :rows="rows"
      :columns="columns"
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
import { onMounted, ref } from 'vue'
import axios from 'axios'
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
setup(){
    const columns=[
         { name: 'tgl', align: 'left', label: 'TANGGAL PEMINJAMAN', field: 'calories', sortable: true },
         { name: 'mulai', align: 'left', label: 'JAM MULAI', field: 'mulai', sortable: true },
         { name: 'selesai', align: 'left', label: 'JAM SELESAI', field: 'selesai', sortable: true },
         { name: 'kegiatan', align: 'left', label: 'KEGIATAN', field: 'kegiatan', sortable: true },
    ]
    const rows=ref([])
    const loading=ref(false)
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
    return{
        columns,
        rows,
        loading,
        dateTime,
        day,
    }
}
}
</script>

<style>

</style>
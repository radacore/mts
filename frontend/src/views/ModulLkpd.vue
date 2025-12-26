<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id==2">
        <q-card-section>
          <q-table
          title="Modul Ajar dan LKPD"
          :rows="rows"
          :columns="columns"
          :filter="filter"
          :loading="loading"
          separator="cell"
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
        <template v-slot:top-right>
          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>
        <template v-slot:body-cell-File="props">
          <q-td :props="props">
            <img v-if="props.row.file.split('.').pop()=='pdf'" src="../assets/pdf.png" style="width: 40px">
            <img v-else src="../assets/ppt.png" style="width: 40px;">
          </q-td>
        </template>
        <template v-slot:body-cell-download="props">
          <q-td :props="props">
          <a :href="url+props.row.file" target="_blank">
           <q-btn label="download" color="red" dense rounded style="width:100px"/>
           </a>
          </q-td>
        </template>
        <template v-slot:body-cell-aksi="props">
          <q-td :props="props">
           <q-btn round flat icon="delete" color="red" size="md" class="q-mx-sm"/>
           <q-btn round flat icon="archive" color="dark" size="md"/>
          </q-td>
        </template>
          </q-table>
        </q-card-section>
      </q-card>
    </div>    
  </q-page>
</template>
<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters, mapState } from 'vuex';
export default {
setup(){
  const columns=[
    { name: 'File', align: 'left', label: 'FILE', sortable: true },
    { name: 'Ket', align: 'left', label: 'Keterangan Modul/LKPD',field:'judul', sortable: true },
    { name: 'download', align: 'left', label: 'Download', sortable: true },
    { name: 'aksi', align: 'left', label: 'Owner', sortable: true },
  ]
  return{
    columns,
    rows:ref([]),
    loading:ref(false),
    filter:ref(null),
  }
},
computed:{
  ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
  ...mapState("kontrol",["url"])
},
methods:{
  async getData(){
    await axios.get("filemodul").then((response)=>{
      this.rows=response.data
    })
  }
},
created(){
this.getData()
}
}
</script>




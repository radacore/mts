<template>
    <q-list style="width:100%">
        <q-item dense> 
          <q-item-section>
            <q-item-label>Alat dan Bahan</q-item-label>
          </q-item-section>
  
          <q-item-section side top>
            <q-btn round flat icon="fas fa-plus-circle" size="sm" color="primary" @click="modalInv=true"/>
          </q-item-section>
        </q-item>
        <q-separator inset />
        <q-item v-for="row in datas" :key="row.id">
          <q-item-section>
            <q-item-label>{{row.inventaris.nabar}}</q-item-label>
            <q-item-label caption>
                <q-chip outline color="light-green-10" text-color="white" icon="fas fa-flask" dense>
                {{row.inventaris.vol}}
                </q-chip>
                <q-chip outline color="pink-7" text-color="green" icon="far fa-lightbulb" dense>
                {{row.inventaris.kondisi}}
                </q-chip>
                </q-item-label>
          </q-item-section>
  
          <q-item-section side top>
           <q-btn round flat icon="o_more_vert" size="sm">
            <q-menu auto-close  transition-show="jump-up"
            transition-hide="jump-down">
                <q-list style="min-width: 100px" dense>
                  <q-item clickable @click="hapus(row.id)">
                    <q-item-section>Hapus</q-item-section>
                  </q-item>
              </q-list>
             </q-menu>     
            </q-btn>
          </q-item-section>
        </q-item>
        <q-separator inset />
      </q-list>
    <!-- DIALOG -->
    <q-dialog v-model="modalInv">
        <q-card style="width: 700px; max-width: 80vw;">
          <q-card-section>
            <q-table
            :rows="rows"
            :columns="columns"
            :selected-rows-label="enterSelect"
            :filter="filter"
            selection="multiple"
            v-model:selected="selected"
            row-key="id"
            flat
            dense
          >
          <template v-slot:top-right>
            <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </template>
          <template v-slot:top-left>
            <q-btn label="Pakai" dense :disable="!selected.length" unelevated no-caps style="width:80px" color="green-7" @click="set" />
          </template>
          </q-table>
          </q-card-section>
        </q-card>
      </q-dialog>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios'
export default {
props:["katalog_id"],
setup(){
    const selected = ref([])
    const rows = ref([]);
    const pilihan=ref([])
    const columns = [
        { name: 'nabar', align: 'left', label: 'Nama Alat/Bahan', field: 'nabar', sortable: true },
        { name: 'katalog', align: 'left', label: 'Katalog', field: 'katalog', sortable: true },
        { name: 'vol', align: 'left', label: 'Volume', field: 'vol', sortable: true },
    ]
    return{
        columns,
        selected,
        modalInv:ref(false),
        rows,
        pilihan,
        filter:ref(null),
        datas:ref([]),


        getSelectedString() {
        return selected.value.length === 0
          ? ""
          : `${selected.value.length} record${
              selected.value.length > 1 ? "s" : ""
            } selected of ${rows.length}`;
            
      },

    }
},
watch:{
  katalog_id(){
    this.getKatalog();
  }
},
methods:{
    enterSelect() {
      this.pilihan = this.selected.map((e) => e.id);
    },
    async getInventaris(){
        await axios.get("inventaris").then((response)=>{
            this.rows=response.data
        })
    },
    async set(){
        await axios.post("inventaris/pilih/"+this.katalog_id+"/"+this.pilihan).then((response)=>{
            this.selected=[]
            this.pilihan=[]
            this.modalInv=false
            this.getKatalog();
            this.$store.commit('kontrol/SET_TRIGER')
            this.$toast.success(`berhasil Tambahkan`)
            return response
        })
    },
    async getKatalog(){
        await axios.get("katalog/data/"+this.katalog_id).then((response)=>{
            this.datas=response.data
        })
    },
    async hapus($id)
    {
        await axios.delete("katalog/data/"+$id).then((response)=>{
            this.getKatalog()
            this.$store.commit('kontrol/SET_TRIGER')
            this.$toast.success(`berhasil dihapus`)
            return response
        })
    }
},
created(){
this.getInventaris()
this.getKatalog();
}
}
</script>


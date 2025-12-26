<template>
    <div>
      <q-btn round icon="fact_check" color="primary" text-color="white" @click="modal=true" size="sm"/>
      <q-dialog v-model="modal">
      <q-card style="width: 700px; max-width: 80vw;">
        <q-card-section>
          <q-table
          :rows="datas"
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
        <template v-slot:body-cell-nabar="props">
          <q-td :props="props">
            {{props.row.nabar}}
          </q-td>
        </template>
        <template v-slot:body-cell-jml="props">
          <q-td :props="props">
            {{props.row.jml}}
          </q-td>
        </template>
        <template v-slot:body-cell-minta="props">
          <q-td :props="props">
              <q-avatar size="sm" text-color="white" color="secondary">
                  {{props.row.minta}}
              </q-avatar>
          </q-td>
        </template>
        <template v-slot:body-cell-diberi="props">
          <q-td :props="props">
              <q-avatar size="sm" text-color="white" color="primary">
                  {{props.row.diberi}}
              </q-avatar>
              <q-popup-edit v-model="props.row.diberi" title="diajukan"  buttons v-slot="scope">
                <q-input type="number" v-model="scope.value" dense autofocus   />
            </q-popup-edit>
          </q-td>
        </template>
        <template v-slot:body-cell-aksi="props">
          <q-td :props="props">
            <save-jumlah2 :id="props.row.jpid" :diberi="props.row.diberi"/>
          </q-td>
        </template>
        </q-table>
        </q-card-section>
        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn flat label="OK" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
    </div>
  </template>
  
  <script>
  import { ref } from '@vue/reactivity'
  import axios from 'axios';
  import { mapState } from 'vuex';
  import SaveJumlah2 from './SaveJumlah2.vue';
  export default {
  components:{
  SaveJumlah2,
  },
  props:["plid","katalog_id"],
  setup(){
      const columns=[
          { name: 'nabar', label: 'Alat/Bahan', align:'left' },
          { name: 'jml', label: 'Tersedia',align:'left' },
          { name: 'minta', label: 'Diajukan', align:'left' },
          { name: 'diberi', label: 'Diberikan', field:'diberi', align:'left' },
          { name: 'aksi', label: 'Aksi', align:'left' },
      ]
      return{
          columns,
          modal:ref(false),
          loading:ref(false),
          datas:ref([]),
          angka:ref(""),
      }
  },
  computed:{
  ...mapState("kontrol",["triger"])
  },
  watch:{
      triger(){
          this.getKatalog();
      }
  },
  methods:{
  async getKatalog(){
      this.loading=true
      await axios.get("filterTopik/"+this.katalog_id+"/"+this.plid).then((response)=>{
          this.datas=response.data
      }).finally(()=>{
          setTimeout(()=>{
              this.loading=false
          },1000);
  
          
      })
  },
  
  },
  created(){
  this.getKatalog()
  }
  }
  </script>
  
  
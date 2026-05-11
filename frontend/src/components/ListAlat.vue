<template>
    <div>
      <q-btn round icon="fact_check" color="primary" text-color="white" @click="modal=true" size="sm"/>
      <q-dialog v-model="modal">
      <q-card style="width: 700px; max-width: 80vw;">
        <q-card-section>
          <q-table
          :rows="filteredDatas"
          :columns="displayedColumns"
          :loading="loading"
          row-key="name"
          dense
          flat
        >
        <template v-slot:top-right>
          <q-input
            v-model="filterAlat"
            borderless
            dense
            debounce="300"
            placeholder="Cari alat"
            clearable
            style="min-width: 220px"
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>
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
              <q-popup-edit
                v-if="user.user.role_id==1 || user.user.role_id==3"
                v-model="props.row.minta"
                title="diajukan"
                buttons
                @save="saveMinta(props.row, $event)"
                v-slot="scope"
              >
                <q-input
                  type="number"
                  v-model.number="scope.value"
                  dense
                  autofocus
                  min="0"
                  :max="props.row.jml"
                  :rules="[
                    val => val !== null && val !== '' || 'Wajib diisi',
                    val => Number(val) >= 0 || 'Tidak boleh kurang dari 0',
                    val => Number(val) <= Number(props.row.jml) || `Maksimal ${props.row.jml}`
                  ]"
                />
              </q-popup-edit>
           
          </q-td>
        </template>
        <template v-slot:body-cell-diberi="props">
          <q-td :props="props">
              <q-avatar size="sm" text-color="white" color="primary">
                  {{props.row.diberi}}
              </q-avatar>
              <q-popup-edit
                v-if="user.user.role_id==2 || user.user.role_id==1"
                v-model="props.row.diberi"
                title="diberikan"
                buttons
                @save="saveDiberi(props.row, $event)"
                v-slot="scope"
              >
                <q-input
                  type="number"
                  v-model.number="scope.value"
                  dense
                  autofocus
                  min="0"
                  :max="props.row.minta"
                  :rules="[
                    val => val !== null && val !== '' || 'Wajib diisi',
                    val => Number(val) >= 0 || 'Tidak boleh kurang dari 0',
                    val => Number(val) <= Number(props.row.minta) || `Maksimal ${props.row.minta}`
                  ]"
                />
              </q-popup-edit>
          </q-td>
        </template>
        <template v-slot:body-cell-aksi="props">
          <q-td :props="props">
            <save-jumlah-alat-2 v-if="user.user.role_id==2" :id="props.row.jpid" :diberi="props.row.diberi"/>
            <save-jumlah-alat v-else :id="props.row.jpid" :minta="props.row.minta"/>
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
  import { mapGetters, mapState } from 'vuex';
  import SaveJumlahAlat from './SaveJumlahAlat.vue';
  import SaveJumlahAlat2 from './saveJumlahAlat2.vue';
  export default {
  components:{
    SaveJumlahAlat,
    SaveJumlahAlat2,
  },
  props:["paid","kat_id"],
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
          filterAlat:ref(""),
      }
  },
  computed:{
  ...mapGetters({
    user: 'auth/user',
  }),
  ...mapState("kontrol",["triger"]),
  displayedColumns() {
    return this.columns.filter(col => col.name !== 'aksi')
  },
  filteredDatas() {
    const roleId = this.user?.user?.role_id
    const keyword = (this.filterAlat || '').toString().toLowerCase().trim()
    let rows = this.datas || []

    if (roleId === 1 || roleId === 2) {
      rows = rows.filter((row) => Number(row.minta || 0) > 0 || Number(row.diberi || 0) > 0)
    }

    if (!keyword) {
      return rows
    }

    return rows.filter((row) => {
      return ['nabar', 'kode', 'kode_barang', 'lokasi', 'satuan', 'merk', 'spesifikasi']
        .some((field) => (row[field] || '').toString().toLowerCase().includes(keyword))
    })
  }
  },
  watch:{
      triger(){
          if (this.modal) {
            this.getKatalog();
          }
      },
      modal(val){
          if (val) {
            this.getKatalog();
          }
      }
  },
  methods:{
  async saveMinta(row, value){
      const form = new FormData
      form.append('id', row.jpid)
      form.append('minta', Number(value))
      await axios.post('jumlahPinjamAlat', form).then(()=>{
          this.$store.commit('kontrol/SET_TRIGER')
      }).catch((error)=>{
          const msg = error.response?.data?.message || 'Gagal memperbarui jumlah diajukan'
          this.$toast.error(msg)
          this.getKatalog()
      })
  },
  async saveDiberi(row, value){
      const form = new FormData
      form.append('id', row.jpid)
      form.append('diberi', Number(value))
      await axios.post('jumlahPinjamAlat2', form).then(()=>{
          this.$store.commit('kontrol/SET_TRIGER')
      }).catch((error)=>{
          const msg = error.response?.data?.message || 'Gagal memperbarui jumlah diberikan'
          this.$toast.error(msg)
          this.getKatalog()
      })
  },
  async getKatalog(){
      this.loading=true
      await axios.get("filterTopikAlat/"+this.kat_id+"/"+this.paid).then((response)=>{
          this.datas=response.data
      }).finally(()=>{
          setTimeout(()=>{
              this.loading=false
          },1000);
      })
  },
  
  },
  }
  </script>
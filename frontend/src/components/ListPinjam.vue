<template>
  <div>
    <q-btn round icon="fact_check" color="primary" text-color="white" @click="modal=true" size="sm"/>
    <q-dialog v-model="modal">
    <q-card style="width: 700px; max-width: 80vw;">
      <q-card-section>
        <q-table
        :rows="filteredDatas"
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
            <q-popup-edit v-if="isGuru" v-model="props.row.minta" title="diajukan" buttons v-slot="scope" @save="submitMinta(props.row)">
                <q-input type="number" v-model="scope.value" dense autofocus />
            </q-popup-edit>
        </q-td>
      </template>
      <template v-slot:body-cell-diberi="props">
        <q-td :props="props">
            <q-avatar size="sm" text-color="white" color="primary">
                {{props.row.diberi}}
            </q-avatar>
            <q-popup-edit v-if="isAdminOrLaboran" v-model="props.row.diberi" title="diberikan" v-slot="scope">
                <q-input type="number" v-model="scope.value" dense autofocus />
                <div v-if="diberiErrors[props.row.jpid]" class="text-negative text-caption q-mt-xs">
                  {{ diberiErrors[props.row.jpid] }}
                </div>
                <div class="row justify-end q-mt-sm">
                  <q-btn flat label="Batal" color="grey" @click="scope.cancel()" />
                  <q-btn flat label="Set" color="primary" @click="submitDiberi(scope, props.row)" />
                </div>
            </q-popup-edit>
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
import { mapState, mapGetters } from 'vuex';
export default {
props:["plid","katalog_id"],
setup(){
    const columns=[
        { name: 'nabar', label: 'Alat/Bahan', align:'left' },
        { name: 'jml', label: 'Tersedia',align:'left' },
        { name: 'minta', label: 'Diajukan', align:'left' },
        { name: 'diberi', label: 'Diberikan', field:'diberi', align:'left' },
    ]
    return{
        columns,
        modal:ref(false),
        loading:ref(false),
        datas:ref([]),
        diberiErrors:ref({}),
    }
},
computed:{
...mapState("kontrol",["triger"]),
...mapGetters({ user: "auth/user" }),
isGuru() {
  return this.user?.user?.role_id === 3;
},
isAdminOrLaboran() {
  return this.user?.user && [1, 2].includes(this.user.user.role_id);
},
filteredDatas() {
  const data = this.datas || [];
  if (this.isGuru) return data;
  return data.filter(row => row.minta > 0);
},
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
async submitMinta(row) {
  const form = new FormData();
  form.append("id", row.jpid);
  form.append("minta", row.minta);
  await axios.post("jumlahPinjam", form).then(() => {
    this.$store.commit('kontrol/SET_TRIGER');
    this.getKatalog();
  });
},
async submitDiberi(scope, row) {
  const jpid = row.jpid;
  const val = parseInt(scope.value);

  if (isNaN(val) || val < 0) {
    this.diberiErrors[jpid] = 'Jumlah tidak valid';
    return;
  }

  if (val > row.minta) {
    this.diberiErrors[jpid] = 'Tidak boleh melebihi jumlah diajukan (' + row.minta + ')';
    return;
  }

  this.diberiErrors[jpid] = '';
  row.diberi = val;

  const form = new FormData();
  form.append("id", jpid);
  form.append("diberi", val);
  await axios.post("jumlahPinjam2", form).then(() => {
    this.$store.commit('kontrol/SET_TRIGER');
    this.getKatalog();
    scope.cancel();
  });
},
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

}
}
</script>


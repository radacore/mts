<template>
  <div>
    <q-btn round icon="fact_check" color="primary" text-color="white" @click="modal=true" size="sm"/>
    <q-dialog v-model="modal">
    <q-card style="width: 700px; max-width: 80vw;">
      <q-card-section>
        <div class="text-h6 q-mb-sm">Daftar Alat/Bahan</div>
        <q-table
        :rows="filteredRows"
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
            <template v-if="Number(role_id) === 3">
              <q-popup-edit v-model="props.row.minta" title="diajukan" buttons v-slot="scope"
                :disable="isLocked">
                  <q-input type="number" v-model="scope.value" dense autofocus
                    :max="Number(props.row.jml)"
                    :error="Number(scope.value) > Number(props.row.jml)"
                    :error-message="'Maksimal ' + props.row.jml"/>
              </q-popup-edit>
            </template>
        </q-td>
      </template>
      <template v-slot:body-cell-diberi="props">
        <q-td :props="props">
            <q-avatar size="sm" text-color="white" color="primary" :class="editableClass()">
                {{props.row.diberi}}
            </q-avatar>
            <template v-if="Number(role_id) !== 3">
              <q-popup-edit v-model="props.row.diberi" title="diberikan" buttons v-slot="scope"
                :disable="isLocked"
                @save="saveDiberi(props.row.jpid, $event, props.row.minta)">
                  <q-input type="number" v-model="scope.value" dense autofocus
                    :max="Number(props.row.minta)"
                    :error="Number(scope.value) > Number(props.row.minta)"
                    :error-message="'Maksimal ' + props.row.minta"/>
              </q-popup-edit>
            </template>
        </q-td>
      </template>
      </q-table>
      <div style="display:none">
        <save-jumlah v-for="row in datas" :key="row.jpid" :id="row.jpid" :minta="row.minta"/>
      </div>
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
import SaveJumlah from './SaveJumlah.vue';
export default {
components:{
SaveJumlah,
},
props:["plid","katalog_id","role_id","status"],
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
        angka:ref(""),
    }
},
computed:{
...mapState("kontrol",["triger"]),
isLocked(){
    return this.status !== 'diajukan'
},
filteredRows(){
    if (Number(this.role_id) === 3) return this.datas
    return this.datas.filter(d => d.minta > 0)
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
editableClass(){
    if (this.isLocked) return ''
    return Number(this.role_id) !== 3 ? 'cursor-pointer' : ''
},
async saveDiberi(id, value, minta){
    const diberi = Number(value)
    if (diberi > Number(minta)) {
        this.$q.notify({
            type: 'negative',
            message: 'Jumlah diberikan tidak boleh melebihi jumlah diajukan.',
            position: 'top'
        })
        return
    }
    this.loading = true
    try {
        const form = new FormData()
        form.append('id', id)
        form.append('diberi', diberi)
        await axios.post('jumlahPinjam2', form)
        this.$q.notify({
            type: 'positive',
            message: 'Jumlah diberikan berhasil disimpan.',
            position: 'top'
        })
    } catch (e) {
        const msg = e.response?.data?.message || 'Gagal menyimpan jumlah diberikan.'
        this.$q.notify({ type: 'negative', message: msg, position: 'top' })
    } finally {
        this.loading = false
    }
}

}
}
</script>


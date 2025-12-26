<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
    <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
        <q-card-section>
           <q-table
              title="Inventaris"
              :rows="rows"
              :columns="columns"
              :filter="filter"
              :pagination="pagination"
              :loading="loading"
              separator="cell"
              row-key="name"
              flat
              dense
              class="my-sticky-column-table my-sticky-header-table"
              
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
            <q-btn label="Insert" class="q-ml-md" icon="o_add" color="green-7" @click="dialogInsert=true" />
          </template>
          <template v-slot:body-cell-spec="props">
            <q-td :props="props">
              <p v-html="props.row.spec"/>
            </q-td>
          </template>
          <template v-slot:body-cell-foto="props">
            <q-td :props="props">
              <div class="images" v-viewer>
              <q-img :src="url+props.row.foto" style="max-width:50px; height:50px" class="rounded-borders"/>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-aksi="props">
            <q-td :props="props">
              <q-btn @click="edit(props.row.id)" round icon="far fa-edit" color="green-7" size="xs" flat/>
              <q-btn @click="konfirmasi(props.row.id)" round icon="fas fa-trash-alt" color="red" size="xs" flat=""/>
              <FotoInv :id="props.row.id"/>
            </q-td>
          </template>
           </q-table>
        </q-card-section>
       </q-card>
       <q-card v-else flat>
        <q-banner dense class="bg-red text-white">
          <template v-slot:avatar>
            <q-icon name="fas fa-user-lock" color="white" />
          </template>
          MAAF, Anda Tidak Berjak Mengakses Halaman ini
          <template v-slot:action>
            <q-btn flat color="white" label="Back To Dashboard" to="/" />
          </template>
        </q-banner>
       </q-card>
       <!-- DIALOG -->
       <q-dialog v-model="dialogInsert">
      <q-card style="width: 700px; max-width: 80vw;">
        <q-toolbar>
            <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Data</span> Inventaris</q-toolbar-title>
            <q-btn flat round dense icon="close" v-close-popup />
          </q-toolbar>
          <q-separator/>
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input outlined v-model="form.noreg" label="No Reg*" class="q-my-sm" color="green-3" dense style="max-width:300px" />
            <q-input outlined v-model="form.katalog" label="Katalog*" class="q-my-sm" color="green-3" dense />
            <q-input outlined v-model="form.nabar" label="Nama Alat/Bahan*" class="q-my-sm" color="green-3" dense />
            <q-editor v-model="form.spec" min-height="5rem" placeholder="Spesifikasi" />
            <q-input outlined v-model="form.satuan" label="Satuan*" class="q-my-sm" color="green-3" dense style="max-width:250px" />
            <q-input outlined v-model="form.vol" label="Volume*" class="q-my-sm" color="green-3" dense style="max-width:250px" />
            <q-input outlined v-model="form.merek" label="Merek" class="q-my-sm" color="green-3" dense />
            <q-input outlined v-model="form.tipe" label="Tipe" class="q-my-sm" color="green-3" dense />
            <q-input outlined v-model="form.produsen" label="Produsen" class="q-my-sm" color="green-3" dense />
            <q-input outlined v-model="form.asal" label="Asal" class="q-my-sm" color="green-3" dense />
            <q-input outlined v-model="form.thn_masuk" label="Tahun Masuk*" class="q-my-sm" color="green-3" dense style="max-width:250px" />
            <q-input outlined v-model="form.thn_pakai" label="Tahun Pakai*" class="q-my-sm" color="green-3" dense style="max-width:250px" />
            <q-input outlined v-model="form.konbaik" label="Kodisi Baik" class="q-my-sm" color="green-3" dense style="max-width:250px" />
            <q-input outlined v-model="form.konrusak" label="Kodisi Rusak" class="q-my-sm" color="green-3" dense style="max-width:250px" />
            <q-input outlined v-model="form.jml" label="Jumlah*" class="q-my-sm" color="green-3" dense style="max-width:250px" />
            <q-input outlined v-model="form.lokasi" autogrow label="Lokasi*" class="q-my-sm" color="green-3" dense />
          </q-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="simpan"/>
          <q-btn label="Batal" color="red-10" @click="batal"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
     <!-- KONFIRMASI -->
     <q-dialog v-model="confirm" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-item>
            <q-item-section side>
              <q-icon color="red" name="fas fa-exclamation-circle" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-subtitle2">Apakah Anda Ingin Menghapus Data ini?</q-item-label>
              <q-item-label caption lines="2">Data Inventaris</q-item-label>
            </q-item-section>
          </q-item>
        </q-card-section>
  
        <q-card-actions align="right">
          <q-btn label="No" color="primary" @click="batal" dense />
          <q-btn label="Yes" color="red" @click="hapus" dense />
        </q-card-actions>
      </q-card>
    </q-dialog>
    </div>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapState,mapGetters } from 'vuex';
import FotoInv from '@/components/FotoInv.vue';
export default {
components:{
  FotoInv
},
setup(){
  
    const columns = [
      {name: "noreg",label: "Nomor REG",align: "left",field:"noreg",sortable: true},
      {name: "katalog",label: "Katalog",align: "left",field:"katalog",sortable: true},
      {name: "albah",label: "Nama Alat/Bahan",align: "left",field:"nabar",sortable: true},
      {name: "spec",label: "Spesifikasi",align: "left",field:"spec",sortable: true},
      {name: "satuan",label: "Satuan",align: "left",field:"satuan",sortable: true},
      {name: "vol",label: "Volume",align: "left",field:"vol",sortable: true},
      {name: "merek",label: "Merek",align: "left",field:"merek",sortable: true},
      {name: "tipe",label: "Tipe",align: "left",field:"tipe",sortable: true},
      {name: "produsen",label: "Produsen",align: "left",field:"produsen",sortable: true},
      {name: "asal",label: "Asal",align: "left",field:"asal",sortable: true},
      {name: "thn_masuk",label: "Tahun Masuk",align: "left",field:"thn_masuk",sortable: true},
      {name: "thn_pakai",label: "Tahun Pakai",align: "left",field:"thn_pakai",sortable: true},
      {name: "jml",label: "Jumlah",align: "left",field:"jml",sortable: true},
      {name: "baik",label: "Baik",align: "left",field:"konbaik",sortable: true},
      {name: "rusak",label: "Rusak",align: "left",field:"konrusak",sortable: true},
      {name: "lokasi",label: "Lokasi",align: "left",field:"lokasi",sortable: true},
      {name: "foto",label: "Photo",align: "left",field:"foto",sortable: true},
      {name: "aksi",align: "left", label:"aksi"},
    ];
    return{
      pagination: {
          rowsPerPage: 10
         },
        columns,
        rows:ref([]),
        filter:ref(null),
        dialogInsert:ref(false),
        confirm:ref(false),
        loading:ref(false),
    }
},
data:()=>({
    form:{
        id:"",
        noreg:"",
        katalog:"",
        nabar:"",
        satuan:"",
        vol:"",
        merek:"",
        tipe:"",
        produsen:"",
        asal:"",
        thn_masuk:"",
        thn_pakai:"",
        jml:"",
        konbaik:"",
        konrusak:"",
        lokasi:"",
        spec:"",
    }
}),
computed:{
...mapState("kontrol",["url"]),
...mapState("kontrol",["triger"]),
...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
},
watch:{
 triger(){
  this.getData();
 }
},
methods:{
    batal(){
        this.dialogInsert=false;
        this.confirm=false;
        this.form.id="";
        this.form.noreg="";
        this.form.katalog="";
        this.form.nabar="";
        this.form.satuan="";
        this.form.vol="";
        this.form.merek="";
        this.form.tipe="";
        this.form.produsen="";
        this.form.asal="";
        this.form.thn_masuk="";
        this.form.thn_pakai="";
        this.form.jml="";
        this.form.konbaik="";
        this.form.konrusak="";
        this.form.lokasi="";
        this.form.spec="";
    },
    konfirmasi($id){
      this.form.id=$id
      this.confirm=true
    },
    async simpan(){
      await axios.post("inventaris", this.form).then((response)=>{
        this.batal()
        this.getData()
        this.$toast.success(`berhasil tersimpan`)
        return response
      }).catch((error)=>{
        this.$toast.error(`Gagal, Mohon Cek kebali`,{
            position: "top",
            duration:2000,
            dismissible:true
         });
        return error
      })
    },
    async getData(){
      this.loading=true
      await axios.get("inventaris").then((response)=>{
        this.rows=response.data
      }).finally(()=>{
        this.loading=false
      })
    },
    async edit($id){
      await axios.get("inventaris/"+$id).then((response)=>{
        this.dialogInsert=true
        this.form.id=response.data.id;
        this.form.noreg=response.data.noreg;
        this.form.katalog=response.data.katalog;
        this.form.nabar=response.data.nabar;
        this.form.satuan=response.data.satuan;
        this.form.vol=response.data.vol;
        this.form.merek=response.data.merek;
        this.form.tipe=response.data.tipe;
        this.form.produsen=response.data.produsen;
        this.form.asal=response.data.asal;
        this.form.thn_masuk=response.data.thn_masuk;
        this.form.thn_pakai=response.data.thn_pakai;
        this.form.jml=response.data.jml;
        this.form.konbaik=response.data.konbaik;
        this.form.konrusak=response.data.konrusak;
        this.form.lokasi=response.data.lokasi;
        this.form.spec=response.data.spec;
      })
    },
    async hapus(){
      await axios.delete("inventaris/"+this.form.id).then((response)=>{
        this.batal()
        this.$toast.success(`berhasil dihapus`)
        this.getData()
        return response
      })
    }
},
created(){
  this.getData();
}
}
</script>
<style lang="sass">
.my-sticky-column-table
  /* specifying max-width so the example can
    highlight the sticky column on any browser window */
  max-width: 100%



  thead tr:nth-child(1) th:nth-child(1)
    /* bg color is important for th; just specify one */
    background-color: rgb(248, 249, 251)

  thead tr:nth-child(1) th:nth-child(2)
    /* bg color is important for th; just specify one */
    background-color: rgb(248, 249, 251)

  thead tr:nth-child(1) th:nth-child(3)
    /* bg color is important for th; just specify one */
    background-color: rgb(248, 249, 251)

    
  td:nth-child(1),
  td:nth-child(1)

    background-color: #F1F8E9

  td:nth-child(2),
  td:nth-child(3)

    background-color: #F1F8E9


  td:nth-child(3)
    background-color: #F1F8E9


    
  th:nth-child(1),
  td:nth-child(1)
    position: sticky
    left: 0
    z-index: 1

  th:nth-child(2),
  td:nth-child(2)
    position: sticky
    left: 120px
    z-index: 2

  th:nth-child(3),
  td:nth-child(3)
    position: sticky
    left: 190px
    z-index: 3
    padding-right: 30px


  

  .my-sticky-header-table
  .q-table thead tr th
        background: rgb(248, 249, 251)

  .q-table--no-wrap td
      white-space: pre-wrap !important

</style>



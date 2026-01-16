<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==1">
          <q-card-section>
            <q-table
            title="Data Slide"
            :rows="rows"
            :columns="columns"
            :filter="filter"
            :loading="loading"
            separator="cell"
            row-key="name"
            flat
            dense
            class="my-sticky-header-table"
            wrap-cells=""
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
          <template v-slot:body-cell-gambar="props">
            <q-td :props="props" :auto-width="true">
                <div class="images" v-viewer>
                    <img :src="url+props.row.gambar" style="width:50px"/>
                </div>
            </q-td>
          </template>
          <template v-slot:body-cell-status="props" >
            <q-td :props="props" style="width:80px">
                <q-btn v-if="props.row.status=='on'" :label="props.row.status" round size="sm" color="green-7" @click="ubahStatus(props.row.id,'off')"/>
                <q-btn v-else :label="props.row.status" round size="sm" color="red" @click="ubahStatus(props.row.id,'on')"/>
            </q-td>
          </template>
          <template v-slot:body-cell-aksi="props">
            <q-td :props="props" style="width:90px">
              <q-btn @click="edit(props.row.id)" round icon="far fa-edit" color="green-7" size="xs" flat/>
              <q-btn @click="konfirmasi(props.row.id)" round icon="fas fa-trash-alt" color="red" size="xs" flat=""/>
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
              MAAF, Anda Tidak Berhak Mengakses Halaman ini
              <template v-slot:action>
                <q-btn flat color="white" label="Back To Dashboard" to="/" />
              </template>
            </q-banner>
        </q-card>
    </div>
    <!-- DIALOG -->
    <q-dialog v-model="dialogInsert">
        <q-card style="width: 500px; max-width: 80vw;">
          <q-toolbar>
              <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Data</span> Slide</q-toolbar-title>
              <q-btn flat round dense icon="close" v-close-popup />
            </q-toolbar>
            <q-separator/>
          <q-card-section style="max-height: 60vh" class="scroll">
            <q-form>
              <q-input outlined v-model="judul" label="Judul" class="q-my-sm" color="green-3" dense />
              <q-input outlined v-model="ket" autogrow label="Keterangan" class="q-my-sm" color="green-3" dense />
              <q-file outlined v-model="gambar" dense color="green-3" label="pilih gambar">
                <template v-slot:prepend>
                  <q-icon name="attach_file" />
                </template>
              </q-file>
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
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters, mapState } from 'vuex';
export default {
setup(){
    const columns=[
        { name: 'gambar', align: 'left', label: 'Image'  },
        { name: 'judul', align: 'left', label: 'Title', field: 'judul', sortable: true },
        { name: 'ket', align: 'left', label: 'Keterangan', field: 'ket', sortable: true },
        { name: 'status', align: 'left', label: 'status', field: 'status', sortable: true },
        { name: 'aksi', align: 'left', label: 'Aksi' }
    ]
    return{
        columns,
        rows:ref([]),
        loading:ref(false),
        filter:ref(null),
        dialogInsert:ref(false),
        confirm:ref(false),

        id:ref(""),
        judul:ref(""),
        ket:ref(""),
        gambar:ref(null),
        status:ref(""),
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
    batal(){
        this.dialogInsert=false
        this.confirm=false
        this.id=""
        this.judul=""
        this.ket=""
        this.gambar=null
    },
    konfirmasi($id){
        this.id=$id
        this.confirm=true
    },
    async getSlide(){
        this.loading=true
        await axios.get("dataSlide").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            this.loading=false
        })
    },
    async simpan(){
        const form=new FormData
        form.append("id", this.id)
        form.append("judul", this.judul)
        form.append("ket", this.ket)
        form.append("gambar", this.gambar)
    await axios.post("dataSlide", form).then((response)=>{
        this.batal();
        this.$toast.success(`berhasil tersimpan`);
        this.getSlide()
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
    async edit($id){
        await axios.get("dataSlide/"+$id).then((response)=>{
            this.dialogInsert=true
            this.id=response.data.id
            this.judul=response.data.judul
            this.ket=response.data.ket
        })
    },
    async hapus(){
        await axios.delete("dataSlide/"+this.id).then((response)=>{
            this.getSlide()
            this.batal()
            this.$toast.success(`berhasil dihapus`)
            return response
        })
    },
    async ubahStatus($id,$stat){
        await axios.put("dataSlide/"+$id+"/"+$stat).then((response)=>{
            this.$toast.success(`status berubah`)
            this.getSlide()
            return response
        })
    }
},
created(){
this.getSlide();
}
}
</script>

<style>

</style>
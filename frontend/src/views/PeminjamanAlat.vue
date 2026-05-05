<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
        <q-card-section>
          <q-table
                title="Peminjaman Alat"
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
             <template v-slot:top-right>
              <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </template>
            <template v-slot:header-cell-alba="props">
              <q-th :props="props">
                <q-icon name="thermostat_auto" size="1.5em" />
              </q-th>
            </template>
            <template v-slot:header-cell-print="props">
              <q-th :props="props">
                <q-icon name="mark_email_read" size="1.5em" />
              </q-th>
            </template>
            <template v-slot:body-cell-peminjam="props">
              <q-td :props="props">
                {{props.row.user.name}}
              </q-td>
            </template>
            <template v-slot:body-cell-kelas="props">
              <q-td :props="props">
                {{props.row.kelas.kelas}}
              </q-td>
            </template>
            <template v-slot:body-cell-tgl_pakai="props">
              <q-td :props="props">
                {{dateTime(props.row.tgl_pakai)}}
              </q-td>
            </template>
            <template v-slot:body-cell-tgl_kembali="props">
              <q-td :props="props">
                {{dateTime(props.row.tgl_kembali)}}
              </q-td>
            </template>
            <template v-slot:body-cell-topik="props">
              <q-td :props="props">
                {{props.row.katalog.topik}}
              </q-td>
            </template>
            <template v-slot:body-cell-lkpd="props">
              <q-td :props="props">
               <div v-if="props.row.lkpd">
                <a :href="url+props.row.lkpd" target="_blank">
                  <img src="../assets/pdf.png" style="max-width:25px;"/>
                </a>
               </div>
              </q-td>
            </template>
            <template v-slot:body-cell-print="props">
              <q-td :props="props">
               <div v-if=" props.row.status=='dikembalikan'">
               <bukti-alat
                  :peminjam="props.row.user.name" :topiks="props.row.katalog.topik"
                  :kelas="props.row.kelas.kelas"
                  :jam_start="props.row.jam_pakai"
                  :jam_end="props.row.jam_selesai"
                  :tanggal="props.row.tgl_pakai"
                  :tgl_kembalis="props.row.tgl_kembali"
                  :pid="props.row.id"
                  :kid="props.row.katalog_id"
                  :dt="props.row.updated_at"
                  :nip="props.row.user.bioguru ? props.row.user.bioguru.nip : '-'"
                  :hp="props.row.user.bioguru ? props.row.user.bioguru.hp : '-'"
                  :lokasi="props.row.lokasi"
               />
               </div>
              </q-td>
            </template>
            <template v-slot:body-cell-status="props">
              <q-td :props="props">
               <div class="column q-gutter-xs">
                  <q-chip v-if="props.row.status=='disetujui'" color="green-7" text-color="white" icon="o_check_circle" dense>
                      {{props.row.status}}
                  </q-chip>
                  <q-chip v-else-if="props.row.status=='ditolak'" color="red-7" text-color="white" icon="cancel" dense>
                      {{props.row.status}}
                  </q-chip>
                  <q-chip v-else-if="props.row.status=='dikembalikan'" color="grey" text-color="white" icon="o_check_circle" dense>
                      {{props.row.status}}
                  </q-chip>
                  <q-chip v-else color="yellow-7" text-color="white" icon="pending" dense>
                      {{props.row.status}}
                  </q-chip>
                  <q-item-label v-if="props.row.status==='ditolak'" caption class="text-red-9" style="max-width:260px;white-space:normal;line-height:1.2;">
                    {{ alasanPenolakanLabel(props.row.alasan_penolakan) }}
                  </q-item-label>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-alba="props">
              <q-td :props="props">
               <div class="row justify-around">
                  <list-pinjam-alat :paid="props.row.id" :katalog_id="props.row.katalog_id"/>
               </div>
              </q-td>
            </template>
            <template v-slot:body-cell-aksi="props">
              <q-td :props="props">
                <q-btn-dropdown color="green-7" label="Proses" dense unelevated style="width:150px">
                    <q-list>
                      <q-item v-if="props.row.status !=='dikembalikan'" clickable v-close-popup @click="proses(props.row.id,'disetujui')">
                        <q-item-section>
                          <q-item-label>Disetujui</q-item-label>
                        </q-item-section>
                      </q-item>
              
                      <q-item v-if="props.row.status=='diajukan'" clickable v-close-popup @click="bukaDialogPenolakan(props.row.id)">
                        <q-item-section>
                          <q-item-label>Ditolak</q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item v-if="props.row.status =='disetujui'" clickable v-close-popup @click="proses(props.row.id,'dikembalikan')">
                        <q-item-section>
                          <q-item-label>Dikembalikan</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-btn-dropdown>
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>

      <q-dialog v-model="dialogAlasanPenolakan" persistent>
        <q-card style="width: 460px; max-width: 90vw;">
          <q-card-section>
            <div class="text-subtitle1 text-red-8">Alasan Penolakan Peminjaman Alat</div>
            <q-input
              v-model="alasanPenolakanInput"
              type="textarea"
              autogrow
              outlined
              dense
              class="q-mt-md"
              maxlength="2000"
              counter
              label="Alasan Penolakan *"
              placeholder="Tuliskan alasan kenapa pengajuan ditolak"
            />
          </q-card-section>
          <q-card-actions align="right">
            <q-btn flat label="Batal" color="grey-7" @click="tutupDialogPenolakan" />
            <q-btn label="Simpan Penolakan" color="red-7" unelevated @click="kirimPenolakan" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>    
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import { mapGetters,mapState } from 'vuex';
import axios from 'axios';
import ListPinjamAlat from "@/components/ListPinjamAlat.vue"
import BuktiAlat from '@/components/BuktiAlat.vue';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
components:{
ListPinjamAlat,
BuktiAlat,
},
setup(){
  const columns = [
        { name: 'peminjam', align: 'left', label: 'Peminjam', sortable: true },
        { name: 'topik', align: 'left', label: 'topik', sortable: true },
        { name: 'tgl_pakai', align: 'left', label: 'Tanggal Penggunaan', field:'tgl_pakai', sortable: true },
        { name: 'jam_pakai', align: 'left', label: 'Jam', field:'jam_pakai', sortable: true },
        { name: 'tgl_kembali', align: 'left', label: 'Tanggal Pengembalian', field:'tgl_kembali', sortable: true },
        { name: 'jam_kembali', align: 'left', label: 'Jam', field:'jam_kembali', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', field:'kelas', sortable: true },
        { name: 'jam', align: 'left', label: 'Jam Ke', field:'jam', sortable: true },
        { name: 'lokasi', align: 'left', label: 'Lokasi', field:'lokasi', sortable: true },
        { name: 'Keperluan', align: 'left', label: 'Keperluan', field:'keperluan', sortable: true },
        { name: 'status', align: 'center', label: 'Status', field:'status', sortable: true },
        { name: 'alba', align: 'center'},
        { name: 'lkpd', align: 'left', label: 'LKPD', field:'lkpd', sortable: true },
        { name: 'print', align: 'center'},
        { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
  return{
    columns,
    loading:ref(false),
    filter:ref(null),
    rows:ref([]),
    dialogAlasanPenolakan:ref(false),
    prosesRowId:ref(null),
    alasanPenolakanInput:ref(''),
  }
},
computed:{
  ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol",["url"]),
},
methods:{
  dateTime(value) {
      return moment(value).format('LL');
    },
  alasanPenolakanLabel(alasan) {
      const value = (alasan || '').toString().trim()
      return value || 'Tidak ada alasan (data lama)'
    },
  async getData(){
    this.loading=true
        await axios.get("peminjaman/alat").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            this.loading=false
        })
  },
  bukaDialogPenolakan($id){
      this.prosesRowId=$id
      this.alasanPenolakanInput=''
      this.dialogAlasanPenolakan=true
    },
  tutupDialogPenolakan(){
      this.dialogAlasanPenolakan=false
      this.prosesRowId=null
      this.alasanPenolakanInput=''
    },
  async kirimPenolakan(){
      const alasan = (this.alasanPenolakanInput || '').toString().trim()
      if (!alasan) {
        this.$toast.error('Alasan penolakan wajib diisi')
        return
      }

      await this.proses(this.prosesRowId,'ditolak', alasan)
      this.tutupDialogPenolakan()
    },
  async proses($id,$data,$alasanPenolakan=null){
        const payload = $data === 'ditolak'
          ? { alasan_penolakan: ($alasanPenolakan || '').toString().trim() }
          : {}

        await axios.put("peminjaman/alat/"+$id+"/"+$data, payload).then((response)=>{
            this.getData();
            this.$toast.success('Status peminjaman berhasil diperbarui')
            return response
        }).catch((error)=>{
            const msg = error.response?.data?.message || 'Gagal memperbarui status peminjaman'
            this.$toast.error(msg)
            return error
        })
    }
},
created(){
this.getData();
}
}
</script>

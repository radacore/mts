<template>
<q-page class="q-pa-sm">
  <div class="row">
    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
      <q-card style="width:100p%;height:410px" class="q-mx-md q-my-sm">
        <q-card-section class="gradasi text-white" style="min-height:180px">
          <div class="text-h6">Informasi Alat dan Bahan</div>
          <div class="text-subtitle2">Laboratorium IPA</div>
        </q-card-section>
        <q-card-section>
          <q-card class="bg-white absolute-center bayangan" style="width:90%; height:150px;border-radius:10px;margin-top:-20px">
            <div class="row justify-between">
              <q-card-section>
                <span class="text-h5 text-primary">{{total_inv}}</span> 
                <p class="text-caption text-grey">Alat & Bahan Praktikum</p> 
              </q-card-section>
              <q-card-section>
                <span class="text-h5 text-green-7">{{kondisi}}</span>
                <p class="text-caption text-grey">Kondisi Baik</p> 
              </q-card-section>
            </div>
              <q-linear-progress rounded size="5px" :value="value" color="deep-orange" style="width:90%" class="q-mx-md" />
            <q-card-actions>
              <q-btn flat round icon="fas fa-chart-bar" color="green" />
              <q-btn flat text-color="deep-orange">
                {{ kondisiPersen }} %
              </q-btn>
             
            </q-card-actions>
          </q-card>
          <q-card class="bg-white absolute-center bayangan" style="width:90%; height:100px; margin-top:120px;border-radius:10px">
            <q-card-section>
               <div class="row justify-between items-center">
                 <div>
                     <span class="text-h5 text-red">{{ rusak }}</span>
                     <p class="text-caption text-grey">Kondisi Rusak</p>
                  </div>
                  <q-circular-progress
                     :value="rusakPersen"
                     size="50px"
                     :thickness="0.22"
                     color="red"
                    track-color="grey-3"
                    class="q-ma-md"
                 />
               </div>
            </q-card-section>
          </q-card>
        </q-card-section>
      </q-card>
    </div>
    <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
      <q-card style=" min-height:400px" class="q-mx-md q-my-sm bayang">
        <q-card-section>
          <jadwal-lab/>
        </q-card-section>
      </q-card>
    </div>
    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
      <q-card class="q-mx-md q-my-sm bayangan" style="border-radius: 12px;">
        <q-card-section class="gradasi text-white" style="min-height: 90px;">
          <div class="text-h6">Menunggu Persetujuan</div>
          <div class="text-subtitle2">Pengajuan yang perlu diproses</div>
        </q-card-section>
        <q-list separator>
          <q-item>
            <q-item-section avatar>
              <q-icon name="o_science" color="green-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Penggunaan Lab</q-item-label>
              <q-item-label caption>Status: diajukan</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-green-8">{{ pinjamLabPending }}</span>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="o_biotech" color="green-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Peminjaman Alat</q-item-label>
              <q-item-label caption>Status: diajukan</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-green-8">{{ pinjamAlatPending }}</span>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="o_alt_route" color="green-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Kegiatan Lain</q-item-label>
              <q-item-label caption>Status: diajukan</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-green-8">{{ pinjamLainPending }}</span>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card>
    </div>
     <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
      <q-card style=" min-height:400px" class="q-mx-md q-my-sm bayang">
        <q-card-section>
          <jadwal-lain/>
        </q-card-section>
      </q-card>
    </div>
  </div>
</q-page>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios';
import JadwalLab from '@/components/JadwalLab.vue';
import JadwalLain from '@/components/JadwalLain.vue';


export default {
  name: 'HomeView',
  components: {
    JadwalLab,
    JadwalLain
  },
  setup(){
    return{
      total_inv:ref(0),
      kondisi:ref(0),
      rusak:ref(0),
      pinjamLabPending: ref(0),
      pinjamAlatPending: ref(0),
      pinjamLainPending: ref(0),
    }
  },
  computed:{
    value: function(){
      const totalKondisi = this.kondisi + this.rusak
      return totalKondisi > 0 ? this.kondisi / totalKondisi : 0
    },
    kondisiPersen() {
      const totalKondisi = this.kondisi + this.rusak
      return totalKondisi > 0 ? Math.round((this.kondisi / totalKondisi) * 100) : 0
    },
    rusakPersen() {
      const totalKondisi = this.kondisi + this.rusak
      return totalKondisi > 0 ? (this.rusak / totalKondisi) * 100 : 0
    },
  },
  methods:{
    async getStat(){
      await axios.get("statistik").then((response)=>{
        this.total_inv=response.data.inv 
        this.kondisi=response.data.kondisi
        this.rusak=response.data.rusak
        this.pinjamLabPending = response.data.pinjam_lab_pending || 0
        this.pinjamAlatPending = response.data.pinjam_alat_pending || 0
        this.pinjamLainPending = response.data.pinjam_lain_pending || 0
      })
    }
  },
  created(){
    this.getStat()
  }
}
</script>
<style lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important

.gradasi
  background: linear-gradient(to right, #355924, #73f261)
</style>

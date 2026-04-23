<template>
<q-page class="q-pa-sm">
  <div class="row">
    <div v-if="showInventoryCard" class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
      <q-card class="q-mx-md q-my-sm bayangan" style="border-radius: 12px;">
        <q-card-section class="gradasi text-white" style="min-height: 90px;">
          <div class="text-h6">Informasi Alat dan Bahan</div>
          <div class="text-subtitle2">Laboratorium IPA</div>
        </q-card-section>
        <q-list separator>
          <q-item>
            <q-item-section avatar>
              <q-icon name="o_inventory_2" color="green-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Alat & Bahan Praktikum</q-item-label>
              <q-item-label caption>Total inventaris</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-green-8">{{ total_inv }}</span>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="o_check_circle" color="green-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Kondisi Baik</q-item-label>
              <q-item-label caption>Siap digunakan</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-green-8">{{ kondisi }}</span>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="o_report_problem" color="orange-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Kondisi Rusak</q-item-label>
              <q-item-label caption>Perlu penanganan</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-orange-8">{{ rusak }}</span>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card>
    </div>
    <div v-if="showApprovalCard" class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
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

    <div v-if="showApprovalCard" class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
      <q-card class="q-mx-md q-my-sm bayangan" style="border-radius: 12px;">
        <q-card-section class="gradasi text-white" style="min-height: 90px;">
          <div class="text-h6">Stok Kritis</div>
          <div class="text-subtitle2">Alat/Bahan perlu restock</div>
        </q-card-section>
        <q-list separator>
          <q-item>
            <q-item-section avatar>
              <q-icon name="o_warning_amber" color="orange-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Stok Menipis</q-item-label>
              <q-item-label caption>Stok 1 - {{ batasStokMenipis }} unit</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-orange-8">{{ stokMenipis }}</span>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="o_remove_shopping_cart" color="red-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Stok Habis</q-item-label>
              <q-item-label caption>Stok 0 unit</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-red-8">{{ stokHabis }}</span>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="o_inventory" color="green-7" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Butuh Tindakan</q-item-label>
              <q-item-label caption>Total item prioritas</q-item-label>
            </q-item-section>
            <q-item-section side>
              <span class="text-h6 text-green-8">{{ stokKritis }}</span>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card>
    </div>

    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
      <q-card style=" min-height:400px" class="q-mx-md q-my-sm bayang">
        <q-card-section>
          <jadwal-lab/>
        </q-card-section>
      </q-card>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
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
import { mapGetters } from 'vuex';


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
      stokMenipis: ref(0),
      stokHabis: ref(0),
      stokKritis: ref(0),
      batasStokMenipis: ref(5),
    }
  },
  computed:{
    ...mapGetters({
      authenticated: 'auth/authenticated',
      user: 'auth/user',
    }),
    showApprovalCard() {
      if (!this.authenticated || !this.user || !this.user.user) return false
      return this.user.user.role_id === 1 || this.user.user.role_id === 2
    },
    showInventoryCard() {
      if (!this.authenticated || !this.user || !this.user.user) return false
      return this.user.user.role_id === 1 || this.user.user.role_id === 2
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
        this.stokMenipis = response.data.stok_menipis || 0
        this.stokHabis = response.data.stok_habis || 0
        this.stokKritis = response.data.stok_kritis || 0
        this.batasStokMenipis = response.data.batas_stok_menipis || 5
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

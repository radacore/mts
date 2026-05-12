<template>
  <q-dialog v-model="show" persistent>
    <q-card style="width: 760px; max-width: 95vw;">
      <q-card-section class="row items-center q-pb-sm">
        <div>
          <div class="text-subtitle1 text-green-8">Pengembalian Alat - Peminjaman #{{ pinjamAlatId }}</div>
          <div class="text-caption text-grey-7">
            Isi jumlah <b>rusak</b> dan <b>hilang</b> per item. Sisanya otomatis dianggap utuh.
          </div>
        </div>
      </q-card-section>
      <q-separator/>
      <q-card-section style="max-height: 60vh" class="scroll q-pt-sm">
        <q-banner v-if="adaHabisPakai" class="bg-orange-1 text-orange-10 q-mb-sm" dense rounded>
          <template v-slot:avatar>
            <q-icon name="info" />
          </template>
          Barang <b>habis pakai</b> sudah dikurangi dari stok saat pengajuan disetujui,
          jadi tidak perlu diisi kondisinya di sini.
        </q-banner>
        <q-table
          :rows="items"
          :columns="columns"
          row-key="jpid"
          dense flat hide-bottom
          :pagination="{ rowsPerPage: 0 }"
          :loading="loading"
          no-data-label="Tidak ada item yang diberikan."
        >
          <template v-slot:body-cell-nabar="props">
            <q-td :props="props">
              <div class="row items-center q-gutter-xs">
                <span>{{ props.row.nabar }}</span>
                <q-chip v-if="props.row.jenis_barang === 'habis_pakai'" dense size="sm"
                        color="orange-3" text-color="orange-10" class="q-ma-none">
                  habis pakai
                </q-chip>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-rusak="props">
            <q-td :props="props">
              <q-input
                v-if="props.row.jenis_barang !== 'habis_pakai'"
                v-model.number="props.row.rusak"
                type="number" dense outlined
                :min="0" :max="props.row.diberi"
                style="width:90px;"
                @update:model-value="clampRow(props.row)"
              />
              <span v-else class="text-grey-6">—</span>
            </q-td>
          </template>
          <template v-slot:body-cell-hilang="props">
            <q-td :props="props">
              <q-input
                v-if="props.row.jenis_barang !== 'habis_pakai'"
                v-model.number="props.row.hilang"
                type="number" dense outlined
                :min="0" :max="Math.max(props.row.diberi - (Number(props.row.rusak)||0), 0)"
                style="width:90px;"
                @update:model-value="clampRow(props.row)"
              />
              <span v-else class="text-grey-6">—</span>
            </q-td>
          </template>
          <template v-slot:body-cell-utuh="props">
            <q-td :props="props">
              <span v-if="props.row.jenis_barang !== 'habis_pakai'" class="text-green-8 text-weight-medium">
                {{ utuhRow(props.row) }}
              </span>
              <span v-else class="text-grey-6">— (sudah keluar)</span>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
      <q-separator/>
      <q-card-actions align="right" class="q-pa-md">
        <q-btn flat label="Batal" color="grey-7" @click="tutup" :disable="submitting" />
        <q-btn label="Simpan Pengembalian" color="green-7" unelevated
               :loading="submitting" @click="kirim" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import axios from 'axios'

export default {
  name: 'DialogPengembalianAlat',
  props: {
    modelValue: { type: Boolean, default: false },
    pinjamAlatId: { type: [Number, String], default: null },
    katalogId: { type: [Number, String], default: null },
  },
  emits: ['update:modelValue', 'submitted'],
  data() {
    return {
      items: [],
      loading: false,
      submitting: false,
      columns: [
        { name: 'nabar', label: 'Nama Alat', field: 'nabar', align: 'left' },
        { name: 'diberi', label: 'Diberikan', field: 'diberi', align: 'center' },
        { name: 'rusak', label: 'Rusak', align: 'center' },
        { name: 'hilang', label: 'Hilang', align: 'center' },
        { name: 'utuh', label: 'Utuh', align: 'center' },
      ],
    }
  },
  computed: {
    show: {
      get() { return this.modelValue },
      set(v) { this.$emit('update:modelValue', v) },
    },
    adaHabisPakai() {
      return this.items.some(r => r.jenis_barang === 'habis_pakai')
    },
  },
  watch: {
    modelValue(v) {
      if (v) this.muatItems()
      else this.items = []
    },
  },
  methods: {
    utuhRow(row) {
      return Math.max(
        Number(row.diberi || 0) - (Number(row.rusak) || 0) - (Number(row.hilang) || 0),
        0
      )
    },
    clampRow(row) {
      // Pastikan rusak & hilang non-negatif dan jumlahnya tidak melebihi diberi
      const diberi = Number(row.diberi || 0)
      row.rusak = Math.max(0, Math.min(Number(row.rusak) || 0, diberi))
      const sisaUntukHilang = Math.max(diberi - row.rusak, 0)
      row.hilang = Math.max(0, Math.min(Number(row.hilang) || 0, sisaUntukHilang))
    },
    async muatItems() {
      if (!this.pinjamAlatId || !this.katalogId) return
      this.loading = true
      try {
        const res = await axios.get(`filterTopikAlat/${this.katalogId}/${this.pinjamAlatId}`)
        this.items = (res.data || [])
          .filter(r => Number(r.diberi) > 0)
          .map(r => ({
            ...r,
            rusak: Number(r.rusak) || 0,
            hilang: Number(r.hilang) || 0,
          }))
      } catch (e) {
        this.$toast?.error?.('Gagal memuat item pengembalian')
      } finally {
        this.loading = false
      }
    },
    tutup() {
      this.show = false
    },
    async kirim() {
      // Validasi: rusak + hilang <= diberi (sudah di-clamp tapi double-check)
      const bermasalah = this.items.find(r =>
        r.jenis_barang !== 'habis_pakai' &&
        ((Number(r.rusak) || 0) + (Number(r.hilang) || 0)) > Number(r.diberi)
      )
      if (bermasalah) {
        this.$toast?.error?.(`Total rusak + hilang tidak boleh melebihi diberikan (${bermasalah.nabar})`)
        return
      }
      const payload = this.items
        .filter(r => r.jenis_barang !== 'habis_pakai')
        .map(r => ({
          jpa_id: r.jpid,
          rusak: Number(r.rusak) || 0,
          hilang: Number(r.hilang) || 0,
        }))
      this.submitting = true
      try {
        await axios.put(
          `peminjaman/alat/${this.pinjamAlatId}/dikembalikan`,
          { pengembalian: payload }
        )
        this.$toast?.success?.('Pengembalian alat berhasil dicatat')
        this.tutup()
        this.$emit('submitted')
      } catch (error) {
        const msg = error.response?.data?.message || 'Gagal mencatat pengembalian'
        this.$toast?.error?.(msg)
      } finally {
        this.submitting = false
      }
    },
  },
}
</script>

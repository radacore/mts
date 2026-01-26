<template>
  <q-page class="flex flex-center">
    <q-card flat style="width: 300px; text-align: center;">
      <q-card-section>
        <q-spinner-ios size="50px" color="green-7" />
        <div class="text-subtitle1 q-mt-md">Memproses login...</div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  name: 'AutoLogin',
  methods: {
    ...mapActions({
      attempt: 'auth/attempt',
    }),
  },
  async created() {
    const token = this.$route.query.token;
    
    if (!token) {
      this.$toast.error('Token tidak valid');
      this.$router.replace({ name: 'home' });
      return;
    }

    try {
      // Simpan token dan ambil info user
      await this.attempt(token);
      
      // Redirect ke ruang praktikum
      this.$router.replace({ name: 'ruang-praktikum' });
    } catch (e) {
      this.$toast.error('Gagal memproses login');
      this.$router.replace({ name: 'home' });
    }
  }
}
</script>

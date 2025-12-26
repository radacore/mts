<template>
  <div>
   <div class="col-12 col-sm-8 col-md-8 absolute-top fit">
    <swiper :modules="modules" 
    :autoplay="{ delay: 5500 }"
    :space-between="30"
    :auto-height="true"
    :loop="true"
    :effect="'fade'"
    :pagination="{ clickable: true }"
    :navigation="true">
      <swiper-slide v-for="row in slides" :key="row.id" >
        <div class="text-center">
          <img :src="url+row.gambar" style="width:100%"/>
        </div>
        <q-card class="absolute-center bg-transparent text-white large-screen-only">
         <q-card-section>
          <p>
            <span class="title text-h5 q-mb-lg">{{row.judul}}</span><br/>
            <span class="text-subtitle1">{{row.ket}}</span>
            </p>
         </q-card-section>
        </q-card>
    </swiper-slide>
    </swiper>
    <section>
      <div class="constrain">
        <my-guru/>
      </div>
    </section>
   </div>
  </div>
</template>

<script>
import { useQuasar } from 'quasar'
import { onBeforeUnmount } from 'vue'
import MyGuru from '@/components/MyGuru.vue'
import { Swiper, SwiperSlide } from 'vue-awesome-swiper'
import { Autoplay,Pagination,EffectFade } from 'swiper'
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/effect-fade'
import axios from 'axios'
import { ref } from '@vue/reactivity'
import { mapState } from 'vuex'
export default {
  name: 'HomeView',
  components: {
    Swiper,
    SwiperSlide,
    MyGuru,
  },
  setup(){
    const $q = useQuasar()
    let timer
    onBeforeUnmount(() => {
      if (timer !== void 0) {
        clearTimeout(timer)
        $q.loading.hide()
      }
    })
    return{
      show:ref(false),
      modules: [Autoplay,Pagination,EffectFade],
      slides:ref([]),
      showLoading (){
      $q.loading.show({
          message: 'E-Lab Digital IPA  <b>process</b> for Student<br/><span class="text-amber text-italic">Please wait...</span>',
          html: true
        })
        timer = setTimeout(() => {
          $q.loading.hide()
          timer = void 0
        }, 3000)
        this.show=true
    },
    }
  },
  computed:{
   ...mapState("kontrol",["url"])
  },
  methods:{
   async getSilde(){
    this.showLoading()
    await axios.get("slides").then((response)=>{
      this.slides=response.data
    })
   }
  },
  created(){
    this.getSilde()
    
  }
}
</script>
<style lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important

.gradasi
  background: linear-gradient(to right, #355924, #73f261)
</style>

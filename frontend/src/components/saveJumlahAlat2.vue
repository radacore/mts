<template>
    <div>
      <q-icon name="o_hourglass_top" color="green-7" size="md"/>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { ref } from '@vue/reactivity';
  
  export default {
  props:["id","diberi"],
  setup(){
      return{
          info:ref(false)
      }
  },
  watch:{
      diberi(){
          this.update()
      }
  },
  methods:{
      async update(){
          const form=new FormData
          form.append("id", this.id)
          form.append("diberi", this.diberi)
          await axios.post("jumlahPinjamAlat2",form).then((response)=>{
              this.$store.commit('kontrol/SET_TRIGER')
              this.info=true
              return response
          }).catch((error)=>{
              const msg = error.response?.data?.message || 'Gagal memperbarui jumlah diberikan'
              this.$toast.error(msg)
              this.$store.commit('kontrol/SET_TRIGER')
          })
      }
  },
  created(){
     
  }
  }
  </script>
  
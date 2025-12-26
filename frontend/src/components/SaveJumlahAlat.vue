<template>
    <div>
      <q-icon name="o_hourglass_top" color="green-7" size="md"/>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { ref } from '@vue/reactivity';
  
  export default {
  props:["id","minta"],
  setup(){
      return{
          info:ref(false)
      }
  },
  watch:{
      minta(){
          this.update()
      }
  },
  methods:{
      async update(){
          const form=new FormData
          form.append("id", this.id)
          form.append("minta", this.minta)
          await axios.post("jumlahPinjamAlat",form).then((response)=>{
              this.$store.commit('kontrol/SET_TRIGER')
              this.info=true
              return response
          })
      }
  },
  created(){
    
  }
  }
  </script>
  
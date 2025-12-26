<template>
    <div>
      
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
          })
      }
  },
  created(){
     
  }
  }
  </script>
  
<template>
    <div style="display: flex; justify-content: center; align-items: center;">
      <div class="register-form">
        <h1>Register</h1>

        <label for="name">Name :</label>  
        <input name="name" ype="text" v-model="name" placeholder="Name">

        <label for="surname">surname :</label>  
        <input name="surname" type="text" v-model="surname" placeholder="Surname">

        <label for="mail">Mail Address :</label>  
        <input name="mail" type="text" v-model="mail" placeholder="Mail address">

        <label for="password">Password :</label>  
        <input name="password" type="password" v-model="password" placeholder="password">

        <label for="confirmPassword">Confirm password :</label>  
        <input name="confirmPassword" type="password" v-model="confirmPassword" placeholder="confirm password">

        <label>Can Add:</label>  
        <label>Yes</label>
        <input name="canAdd" type="radio" v-model="canAdd" v-bind:value="true">
        <label>No</label>
        <input name="canAdd" type="radio" v-model="canAdd" v-bind:value="false">

        <p style="color: red">{{ errorMessage }}</p>

        <input type="button" value="Sign-in" v-on:click="createUser()"/>
      </div>
    </div>
</template>
<script>
import axios from 'axios';

export default{
    data(){
        return{
            name:"",
            surname:"",
            mail:"",
            password:"",
            confirmPassword:"",
            canAdd: false,
            errorMessage : ""
        }

    },
    methods:{
      checkPassword(){
        return this.password === this.confirmPassword ?  true : false
      },
      async createUser(){
        if(this.checkPassword() && this.checkAllFieldsFill()){
          const userData = {
           "name": this.name,
           "surname" : this.surname,
           "email" : this.mail,
           "canAdd" : this.canAdd,
           "password" : this.password, 
          }

          console.log(userData)
          const req = await axios.post("http://localhost/user/register",userData)
          const res = await req.data
          console.log(res)
        } else if(!this.checkPassword())this.errorMessage = "Password / Password verification are not the same"
        else this.errorMessage = "Please fill all fields"
      },
      checkAllFieldsFill(){
        if(!this.name || !this.surname ||!this.password || !this.mail)return false
        return true
      },
    }
}
</script>
<style>
.register-form {
  padding: 1%;
  display: flex;
  flex-direction: column;
  width: 50%;
  margin-top: 6%;
}
</style>
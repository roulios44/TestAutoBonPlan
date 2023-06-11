<template>
    <div class="container">
      <div class="login-form">
        <h1>Login</h1>

        <label for="mail">Mail Address :</label>  
        <input name="mail" type="text" v-model="mail" placeholder="Mail address">

        <label for="password">Password :</label>  
        <input name="password" type="password" v-model="password" placeholder="password">

        <p style="color: red">{{ errorMessage }}</p>

        <input type="button" value="Sign-in" v-on:click="connectUser()"/>
      </div>
      <p style="cursor: pointer;" v-on:click="$router.push('/register')">Pas encore de compte ? Cliquer ici</p>
    </div>
</template>
<script>
import axios from 'axios';

export default{
    data(){
        return{
            mail:"",
            password:"",
            errorMessage : ""
        }

    },
    methods:{

      async connectUser(){
        if(this.checkAllFieldsFill()){
          const userData = {
           "email" : this.mail,
           "password" : this.password, 
          }
          const req = await axios.post("http://localhost/user/login",userData)
          const res = await req.data
          console.log(res)
          localStorage.setItem("userID",res[0].id)
          localStorage.setItem("canAdd",res[0].canAdd)
          this.$router.push("/mainHub")
        } else this.errorMessage = "Please fill all fields"
      },
      checkAllFieldsFill(){
        if(!this.password || !this.mail)return false
        return true
      },
    }
}
</script>
<style>
.login-form {
  padding: 1%;
  display: flex;
  flex-direction: column;
  width: 50%;
  margin-top: 6%;
}
.container{
  display: flex;
  justify-content: center; 
  align-items: center;
  display: flex;
	flex-direction: column;
	flex-wrap: nowrap;
	align-content: center;
}
</style>
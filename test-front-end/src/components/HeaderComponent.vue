<template>
  <div class="header">
    <button @click="toggleMenu" :class="['burger-menu', { 'active': showMenu }]">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <transition name="slide">
      <ul v-if="showMenu" class="menu">
        <li style="cursor:pointer" v-on:click="disconnect()">Se déconnecter</li>
      </ul>
    </transition>
    <div class="logo">
      <img src="@/assets/logo.png">
    </div>
    <div class="sectionTitle">
      <h1>Suivi Hebdo</h1>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showMenu: false
    };
  },
  methods: {
    toggleMenu() {
      this.showMenu = !this.showMenu;
    },
    disconnect(){
      localStorage.removeItem("userID")
      this.$router.push("login")
    }
  }
};
</script>

<style>
.burger-menu {
  margin: 1%;
  box-shadow: #333;
  position: relative;
  z-index: 500;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 75px;
  border: none;
  background-color: #fff;
  height: 50px;
  cursor: pointer;
}

.burger-menu span {
  background-color: #333;
  width: 100%;
  height: 10%;
}

.menu {
  position: fixed;
  margin-top: 5%;
  top: 0;
  left: 0;
  width: 250px;
  height: 100%;
  background-color: #fff;
  box-shadow: 0px 5px 0px rgb(207, 189, 189);

  z-index: 998;
  padding: 20px;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease-in-out;
}

.slide-enter,
.slide-leave-to {
  transform: translateX(-100%);
}
ul{
  list-style-type: none;
}

.header{
  display: flex;
	flex-direction: row;
	flex-wrap: nowrap;
	justify-content: flex-start;
	align-items: stretch;
	align-content: stretch;
  margin: 0;
  padding: 0;
  width: 100%;
  height: 35%;
  min-height: 35px;
  box-shadow: 0px 5px 0px rgb(207, 189, 189);
}

.logo img{
  width: 100%;
  max-width: 200px;
}
.logo{
  display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: center;
	align-items: center;
	align-content: center;
  margin: 0;
  padding: 0;
  width: 25%;
  height: 50%;
  max-width: fit-content;
}

.sectionTitle{
  margin-left: 5%;
  display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: center;
	align-items: center;
	align-content: center;
}

</style>

import { createWebHistory, createRouter } from "vue-router"
import Register from "@/views/RegisterPage.vue"

const routes=[
    {
        path:"/register",
        name: "register",
        component: Register,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
  })
  
  export default router
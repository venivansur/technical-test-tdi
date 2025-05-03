import { defineStore } from 'pinia'
import axios from 'axios'

export const useOrderStore = defineStore('order', {
  state: () => ({
    orders: [],
    selectedOrder: null,
  }),
  actions: {
    async fetchOrders() {
      const res = await axios.get('/orders?_embed=items')
      this.orders = res.data
    },
    async createOrder(orderData) {
      try {
        this.loading = true
        this.error = null
        const response = await axios.post('/orders', orderData)

        this.orders.push(response.data)

        return response.data
      } catch (error) {
        this.error = error
        console.error('Gagal membuat pesanan:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
    async selectOrder(orderId) {
      this.selectedOrder = this.orders.find((order) => order.id === orderId)
    },
  },
  getters: {
    formattedOrders: (state) => {
      return state.orders.map((order) => ({
        ...order,
        total_items: order.items?.length || 0,
        total_price: order.items?.reduce((sum, item) => sum + item.price * item.quantity, 0) || 0,
      }))
    },
  },
})

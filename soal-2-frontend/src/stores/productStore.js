import { defineStore } from 'pinia'
import axios from 'axios'

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchProducts() {
      try {
        this.loading = true
        this.error = null
        const response = await axios.get('/products')
        this.products = response.data
        return response.data
      } catch (error) {
        this.error = error
        console.error('Failed to fetch products:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
    async addProduct(product) {
      try {
        this.loading = true
        const response = await axios.post('/products', product)
        this.products.push(response.data)
        return response.data
      } catch (error) {
        this.error = error
        console.error('Failed to add product:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
    async updateProduct(product) {
      try {
        this.loading = true
        const response = await axios.put(`/products/${product.id}`, product)

        const index = this.products.findIndex((p) => p.id === product.id)
        if (index !== -1) {
          this.products.splice(index, 1, response.data)
        }
        return response.data
      } catch (error) {
        this.error = error
        console.error('Failed to update product:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
    async deleteProduct(id) {
      try {
        this.loading = true
        await axios.delete(`/products/${id}`)

        this.products = this.products.filter((product) => product.id !== id)
      } catch (error) {
        this.error = error
        console.error('Failed to delete product:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
  },
  getters: {
    availableProducts: (state) => {
      return state.products.filter((product) => product.stock > 0)
    },
  },
})

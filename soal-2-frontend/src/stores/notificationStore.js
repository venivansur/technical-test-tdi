export default {
    state: { message: null },
    mutations: {
      setMessage(state, msg) {
        state.message = msg;
        setTimeout(() => state.message = null, 3000);
      }
    }
  }
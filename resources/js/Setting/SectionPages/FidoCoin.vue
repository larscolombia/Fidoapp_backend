<template>
  <CardTitle :title="$t('setting_general_page.coin_configuration')">
    <h2>{{ $t('setting_general_page.coin_configuration') }}</h2>
  </CardTitle>
  <div class="update-form">
    <!-- Barra de notificación -->
    <div v-if="notification" class="alert" :class="notification.type">
      {{ notification.message }}
    </div>

    <form @submit.prevent="updateData">
      <input type="hidden" name="id" v-model="form.id" id="id" />
      <div class="form-group">
        <label for="symbol">{{ $t('coin.symbol') }}</label>
        <input v-model="form.symbol" type="text" id="symbol" class="form-control" required />
      </div>
      <div class="form-group">
        <label for="minimum_recharge">{{ $t('coin.minimum_recharge') }}</label>
        <input v-model.number="form.minimum_recharge" min="1" type="number" id="minimum_recharge" class="form-control" required />
      </div>
      <div class="form-group">
        <label for="conversion_rate">{{ $t('coin.conversion_rate') }}</label>
        <input v-model.number="form.conversion_rate" min="1" type="number" id="conversion_rate" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-primary">{{ $t('coin.update') }}</button>
    </form>
  </div>
</template>

<script>
import CardTitle from '@/Setting/Components/CardTitle.vue';
import axios from 'axios';

export default {
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      form: {
        id: null, // Inicializa como null
        symbol: '',
        minimum_recharge: 1,
        conversion_rate: null,
      },
      notification: null, // Para manejar la notificación
    };
  },
  methods: {
    fetchCurrencyData() {
      axios.get(`/api/coin`) // Obtener la moneda por su ID
        .then(response => {
          this.form = response.data;
          if (!this.form.id) {
            this.form.id = null; // Asegúrate de que sea null si no hay ID
          }
        })
        .catch(error => {
          console.error("Error al obtener los datos:", error);
          this.showNotification(this.$t('coin.load_error'), 'error');
        });
    },
    updateData() {
      axios.post(`/api/coin`, this.form)
        .then(response => {
          this.showNotification(this.$t('coin.success_update'), 'success');
          this.$emit('data-updated', response.data);
        })
        .catch(error => {
          console.error("Error al actualizar los datos:", error);
          this.showNotification(this.$t('coin.error_update'), 'error');
        });
    },
    showNotification(message, type) {
      this.notification = { message, type }; // Establecer el mensaje y tipo
      setTimeout(() => {
        this.notification = null; // Limpiar la notificación después de 5 segundos
      }, 5000);
    },
  },
  mounted() {
    this.fetchCurrencyData();
  },
};
</script>

<style scoped>
.update-form {
  max-width: 600px;
  margin: auto;
}

.alert {
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 5px;
}

.alert.success {
  background-color: #d4edda;
  color: #155724;
}

.alert.error {
  background-color: #f8d7da;
  color: #721c24;
}
</style>

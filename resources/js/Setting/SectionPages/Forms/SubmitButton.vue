<template>
  <div class="d-grid d-md-block setting-footer">
    <button :disabled="IS_SUBMITED" class="btn btn-primary d-flex align-items-center gap-1" name="submit" @click="(e) => emit('click', e)">
      <template v-if="IS_SUBMITED">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Loading...
      </template>
      <template v-else> 
        {{ $t('messages.save') }}
        <i class="fa-solid fa-floppy-disk"></i></template>
    </button>
  </div>
</template>

<script setup>
defineProps({
  IS_SUBMITED: false
})
const emit = defineEmits(['click'])
</script>

<style scoped>
@media only screen and (max-width: 600px) {
  .setting-footer {
  position: fixed;
  width: 100%;
  bottom: 0;
  left: 0;
  }
  .setting-footer>button {
    border-radius: 0;
  }
}
</style>

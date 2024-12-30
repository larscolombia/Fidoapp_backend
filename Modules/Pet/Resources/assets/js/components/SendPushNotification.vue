<template>
  <form @submit.prevent="formSubmit">
      <div class="offcanvas offcanvas-end offcanvas-booking" id="user_send_push_notification" aria-labelledby="form-offcanvasLabel">
        <FormHeader :createTitle="createTitle"></FormHeader>

        <div class="offcanvas-body">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <InputField type="text" class="col-md-12" :is-required="true" :label="$t('booking.lbl_title')"
                  placeholder="" v-model="title" :error-message="errors['title']"
                  :error-messages="errorMessages['title']"></InputField>

                  <div class="form-group col-md-12">
                    <label class="form-label" for="description">{{ $t('booking.lbl_description') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" v-model="description" id="description"></textarea>
                    <span v-if="errorMessages['description']">
                      <ul class="text-danger">
                        <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                      </ul>
                    </span>
                    <div class="text-danger">{{ errors.description }}</div>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="offcanvas-footer border-top">
            <div class="d-grid d-md-flex gap-3 p-3">

              <button v-if="IsLoading==true" class="btn btn-primary d-flex align-items-center gap-1" id="save-button">
                Enviando....
              </button>

              <button v-else class="btn btn-primary d-flex align-items-center gap-1" id="save-button">
                Enviar
                <i class="fa-regular fa-paper-plane"></i>
              </button>
              <button class="btn btn-soft-primary d-flex align-items-center gap-1" type="button" data-bs-dismiss="offcanvas">
                Cancelar
                <i class="icon-Arrow---Right-2"></i>
              </button>
            </div>
          </div>
      </div>
    </form>
</template>
<script setup>
import { ref } from 'vue'

import { useField, useForm } from 'vee-validate'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { SEND_PUSH_NOTIFICATION } from '../constant/customer'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
let translations = {};
const defaultTranslations = {
  required: 'Este campo es obligatorio.',
  string: 'Este campo debe ser una cadena.',
  email: 'Este campo debe ser un correo electrónico válido.',
  min: 'Este campo debe tener al menos :min caracteres.',
  confirmed: 'La confirmación no coincide.',
  not_especial: 'No se permiten caracteres especiales.',
  only_digits: 'El campo debe contener solo dígitos.',
  first_strings_are_allowed: 'Se permiten las primeras cadenas.',
  same_password: 'Las contraseñas deben coincidir.',
};
// Función para cargar las traducciones
async function loadTranslations() {
  // Intentar cargar desde localStorage
  const storedTranslations = localStorage.getItem('translations');

  if (storedTranslations) {
    translations = JSON.parse(storedTranslations);
    console.log('Cargadas traducciones desde localStorage:', translations);
    return; // Salir si ya tenemos traducciones
  }

  try {
    const response = await axios.get('/api/translations');
    translations = response.data;

    // Almacenar en localStorage
    localStorage.setItem('translations', JSON.stringify(translations));
    console.log('Cargadas traducciones desde el servidor:', translations);
  } catch (error) {
    console.error('Error loading translations:', error);
    // Si hay un error, usar los mensajes por defecto
    translations = defaultTranslations;
  }
}
// Llamar a la función para cargar las traducciones
loadTranslations();
function getTranslation(key,default_min = null, default_max = null) {
  // Intenta obtener las traducciones del localStorage
  const storedTranslations = localStorage.getItem('translations');

  if (storedTranslations) {
    const translationsFromStorage = JSON.parse(storedTranslations);
    // Devuelve la traducción correspondiente si existe
    if (translationsFromStorage[key]) {
      if(default_min !== null){
        translationsFromStorage[key].replace(':min', default_min);
      }
      if(default_max !== null){
        translationsFromStorage[key].replace(':max', default_max);
      }
      return translationsFromStorage[key].replace(':attribute', '');
    }
  }

  // Si no se encuentra, devolvemos el mensaje por defecto
  return defaultTranslations[key] || `Missing translation for ${key}`;
}
// props
defineProps({
  createTitle: { type: String, default: '' },

})

const {storeRequest} = useRequest()

const currentId = useModuleId(() => {

  setFormData(defaultData())

}, 'employee_assign')

// Validations
const validationSchema = yup.object({
    title: yup.string().required(getTranslation('required')),
    description: yup.string().required(getTranslation('required')),

  })

  const defaultData = () => {
    errorMessages.value = {}
    return {
        title: '',
        description: ''
    }
  }

  const setFormData = (data) => {
    resetForm({
      values: {
        title: '',
        description: ''
      }
    })
  }


const { handleSubmit, errors,resetForm } = useForm({
  validationSchema
})

const { value: title } = useField('title')
const { value: description } = useField('description')
const errorMessages = ref({})
const IsLoading=ref(false);



// Form Submit
const formSubmit = handleSubmit((values) => {
  IsLoading.value=true
  values.user_id=currentId.value
  storeRequest({ url: SEND_PUSH_NOTIFICATION, body: values}).then((res) => reset_datatable_close_offcanvas(res))

})
// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  IsLoading.value=false
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#user_send_push_notification').hide()
    setFormData(defaultData())
    currentId.value = 0
  } else {
    setFormData(defaultData())
    currentId.value = 0
    bootstrap.Offcanvas.getInstance('#user_send_push_notification').hide()
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}


</script>

<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking"  id="Employee_change_password" aria-labelledby="form-offcanvasLabel">
      <FormHeader :createTitle="createTitle"></FormHeader>


      <div class="offcanvas-body">

        <div class="row">
          <div class="col-12">
            <div class="form-group">

              <InputField type="password" class="col-md-12" :is-required="true" :label="$t('employee.lbl_password')"
                placeholder="" v-model="password" :error-message="errors['password']"
                :error-messages="errorMessages['password']"></InputField>

              <InputField type="password" class="col-md-12" :is-required="true" :label="$t('employee.lbl_confirm_password')"
                placeholder="" v-model="confirm_password" :error-message="errors['confirm_password']"
                :error-messages="errorMessages['confirm_password']"></InputField>

            </div>
          </div>
        </div>
      </div>
      <FormFooter></FormFooter>
      </div>

  </form>
</template>
<script setup>
import { ref } from 'vue'

import { useField, useForm } from 'vee-validate'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { CHANGE_PASSWORD_URL } from '../constant/customer'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
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

}, 'employee_assign')

// Validations
const validationSchema = yup.object({
  password: yup.string()
    .required(getTranslation('required'))
    .min(6,getTranslation('required',6)),
    confirm_password: yup.string()
    .oneOf([yup.ref('password'), null], getTranslation('same_password'))
    .required(getTranslation('required')),
})

const defaultData = () => {
  errorMessages.value = {}
  return {
    password: '',
    confirm_password: '',
  }
}

const setFormData = (data) => {

  resetForm({
    values: {
      password:'' ,
      confirm_password:'',
    }
  })
}


const { handleSubmit, errors,resetForm } = useForm({
  validationSchema
})

const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')
const errorMessages = ref({})

// Form Submit
const formSubmit = handleSubmit((values) => {
  values.user_id=currentId.value
  storeRequest({ url: CHANGE_PASSWORD_URL, body: values}).then((res) => reset_datatable_close_offcanvas(res))

})
// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#Employee_change_password').hide()
    setFormData(defaultData())
    currentId.value = 0
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}


</script>

<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="add-pet-form"
      aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">

        <div class="form-group">
          <div class="text-center">
            <img :src="ImageViewer || props.defaultImage" alt="pet-image"
              class="img-fluid mb-2 avatar avatar-140 avatar-rounded" />

          </div>
          <label class="form-label" for="pet_image">{{ $t('pet.lbl_pet_image') }}</label>
          <input type="file" class="form-control" id="pet_image" @change="fileUpload"
            accept=".jpeg, .jpg, .png, .gif" />
          <span v-if="errorMessages['pet_image']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['pet_image']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.pet_image }}</span>
        </div>

        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('pet.lbl_name')" placeholder=""
          v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>

        <div class="form-group">
          <label class="form-label" for="pettype_id">{{ $t('pet.lbl_pettype') }} <span class="text-danger">*</span>
          </label>
          <Multiselect v-model="pettype_id" :value="pettype_id" v-bind="pettype" id="pettype_id"
            @select="changePettype"></Multiselect>
          <span v-if="errorMessages['pettype_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['pettype_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.pettype_id }}</span>
        </div>

        <div class="form-group">
          <label class="form-label" for="breed">{{ $t('pet.lbl_breed') }}</label>
          <Multiselect v-model="breed" :value="breed" v-bind="breed_list" id="breed"></Multiselect>
          <span v-if="errorMessages['breed']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['breed']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.breed }}</span>
        </div>


        <div v-if="!unknownAge" class="form-group">
          <label class="form-label d-block" for="name">{{ $t('pet.lbl_date_of_birth') }}</label>
          <flat-pickr v-model="date_of_birth" :config="config" class="form-control d-block" />
        </div>

        <InputField v-if="unknownAge" class="col-md-12" type="text" :label="$t('pet.lbl_age')" placeholder=""
          v-model="age" :error-message="errors['age']" :error-messages="errorMessages['age']"></InputField>

        <div class="form-group">
          <input type="checkbox" id="unknown-age" v-model="unknownAge" @change="toggleUnknownAge" />
          <label for="unknown-age" class="form-label">{{ $t('pet.lbl_unknown_age') }}</label>
        </div>


        <div class="form-group col-md-4">
          <label for="" class="form-label w-100">{{ $t('customer.lbl_gender') }}</label>
          <div class="d-flex mt-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" v-model="gender" id="male" value="male" />
              <label class="form-check-label" for="male"> {{ $t('customer.lbl_male') }} </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" v-model="gender" id="female" value="female" />
              <label class="form-check-label" for="female"> {{ $t('customer.lbl_female') }} </label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" v-model="gender" id="other" value="other" />
              <label class="form-check-label" for="other"> {{ $t('customer.lbl_other') }} </label>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="row">
            <InputField class="col-md-6" type="text" :label="$t('pet.lbl_weight')" placeholder="" v-model="weight"
              :error-message="errors['weight']" :error-messages="errorMessages['weight']"></InputField>
            <div class="col-md-6">
              <label class="form-label" for="weight_unit">{{ $t('pet.lbl_weight_unit') }}</label>
              <select class="form-select" v-model="weight_unit">
                <option value="kg">{{ $t('pet.pet_kg') }}</option>
                <option value="lbs">{{ $t('pet.pet_lbs') }}</option>
                <!-- <option value="g">{{ $t('pet.pet_g') }}</option> -->
                <!-- <option value="mg">{{ $t('pet.pet_mg') }}</option> -->
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="row">
            <InputField class="col-md-6" type="text" :label="$t('pet.lbl_height')" placeholder="" v-model="height"
              :error-message="errors['height']" :error-messages="errorMessages['height']"></InputField>
            <div class="col-md-6">
              <label class="form-label" for="height_unit">{{ $t('pet.lbl_height_unit') }}</label>
              <select class="col-md-12 form-select" v-model="height_unit">
                <option value="cm">{{ $t('pet.pet_cm') }}</option>
                <option value="m">{{ $t('pet.pet_m') }}</option>
                <option value="ft">{{ $t('pet.pet_ft') }}</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group ">
          <label class="form-label" for="user_id">{{ $t('pet.lbl_user') }} <span class="text-danger">*</span> </label>
          <Multiselect v-model="user_id" :value="user_id" v-bind="userlist" id="user_id" @select="changeuser"
            :disabled="user_id != ''" required></Multiselect>
          <span v-if="errorMessages['user_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['user_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.user_id }}</span>
        </div>

        <div class="form-group col-md-12">
          <label class="form-label" for="additional_info">{{ $t('pet.lbl_additional_info') }}</label>
          <textarea class="form-control" v-model="additional_info" id="additional_info"></textarea>
          <span v-if="errorMessages['additional_info']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['additional_info']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.additional_info }}</span>
        </div>

        <div class="form-group">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="category-status">{{ $t('pet.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :checked="status" name="status" id="pet-status"
                type="checkbox" v-model="status" />
            </div>
          </div>
        </div>
      </div>
      <div class="d-grid d-md-flex gap-3 pt-5">
        <div class="d-grid d-md-flex gap-3 p-3">
          <button class="btn btn-primary d-flex align-items-center gap-2 fw-600" id="save-button"
            :disabled="isDisable > 0">
            {{ $t('booking.btn_save') }}
            <i class="icon-disk"></i>
          </button>
          <button class="btn btn-soft-primary d-block fw-600" type="button" data-bs-dismiss="offcanvas">
            {{ $t('booking.btn_cancle') }}
            <i class="icon-Arrow---Right-2"></i>
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, PETTYPE_LIST, USER_LIST, BREED_LIST } from '../constant/pets'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'

import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { readFile } from '@/helpers/utilities'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'
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
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: 'Edit Pet' },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' },
})
// const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)
const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

// onMounted(() => {

//   setFormData(defaultData())
// })

// File Upload Function
const ImageViewer = ref(null)
const fileUpload = async (e) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    ImageViewer.value = fileB64
  })
  pet_image.value = file
}


const isDisable = ref(0)

const config = ref({
  dateFormat: 'Y-m-d',
  static: true,
  maxDate: 'today'
})

// Edit Form Or Create Form
const userID = useModuleId(() => {


  setFormData(defaultData())

}, 'add_pet')

// Estado para el checkbox de "Edad Desconocida"
const unknownAge = ref(false)

/*
* Form Data & Validation & Handeling
*/
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    petype_id: '',
    breed: '',
    size: '',
    date_of_birth: '',
    age: '',
    gender: 'male',
    weight: '',
    height: '',
    weight_unit: '',
    height_unit: '',
    user_id: userID.value,
    additional_info: '',
    status: 1,
    pet_image: null,
  }
}

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.pet_image
  resetForm({
    values: {
      name: data.name,
      pettype_id: data.pettype_id,
      breed: data.breed_id,
      size: data.size,
      date_of_birth: data.date_of_birth,
      age: data.age,
      gender: data.gender,
      weight: data.weight,
      height: data.height,
      weight_unit: data.weight_unit,
      height_unit: data.height_unit,
      user_id: data.user_id,
      additional_info: data.additional_info,
      status: data.status,
      pet_image: data.pet_image,
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  isDisable.value = 0;
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#add-pet-form').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}


const numberRegex = /^\d+$/
const decimalRegex = /^\d+(\.\d+)?$/
// Validations
const validationSchema = yup.object({
  name: yup.string().required(getTranslation('required')),
  pettype_id: yup.string().required(getTranslation('required')).matches(/^\d+$/, 'Only numbers are allowed'),
  user_id: yup.string().required(getTranslation('required')).matches(/^\d+$/, 'Only numbers are allowed'),
  weight: yup.string().nullable().matches(/^\d*(\.\d+)?$/, 'Only numbers are allowed'),
  height: yup.string().nullable().matches(/^\d*(\.\d+)?$/, 'Only numbers are allowed'),

})


const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: pettype_id } = useField('pettype_id')
const { value: breed } = useField('breed')
const { value: size } = useField('size')
const { value: date_of_birth } = useField('date_of_birth')
const { value: age } = useField('age')
const { value: gender } = useField('gender')
const { value: weight } = useField('weight')
const { value: height } = useField('height')
const { value: weight_unit } = useField('weight_unit')
const { value: height_unit } = useField('height_unit')
const { value: user_id } = useField('user_id')
const { value: additional_info } = useField('additional_info')
const { value: status } = useField('status')
const { value: pet_image } = useField('pet_image')

const errorMessages = ref({})

const pettype = ref({
  searchable: true,
  options: []
})
const userlist = ref({
  searchable: true,
  options: []
})

const breed_list = ref({
  searchable: true,
  options: []
})

const changePettype = () => {

  let pet_id = 0

  if (pettype_id.value > 0) {

    pet_id = pettype_id.value

  }

  listingRequest({ url: BREED_LIST, data: { id: pet_id } }).then((res) => {
    if (res != '') {
      breed.value = ''
      breed_list.value.options = buildMultiSelectObject(res, { value: 'id', label: 'name' })

    } else {
      breed.value = ''
      breed_list.value.options = []
    }


  })
}


const getPettypeList = () => {
  listingRequest({ url: PETTYPE_LIST, data: { id: 0 } }).then((res) => (pettype.value.options = buildMultiSelectObject(res, { value: 'id', label: 'name' })))
}

const getUserList = () => {
  listingRequest({ url: USER_LIST, data: { id: 0 } }).then((res) => (userlist.value.options = buildMultiSelectObject(res, { value: 'id', label: 'first_name' })))
}

onMounted(() => {
  getPettypeList()
  getUserList()
  //changePettype()
  setFormData(defaultData())
})

// Form Submit
const formSubmit = handleSubmit((values) => {
  isDisable.value = 1;
  storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
})


useOnOffcanvasHide('add-pet-form', () => setFormData(defaultData()))

const toggleUnknownAge = () => {
  if (unknownAge.value) {
    age.value = ''
    date_of_birth.value = ''
  }
}
</script>

<style>
.flatpickr-wrapper {
  width: 100% !important;
  display: block;
}
</style>

<script setup>
import { defineProps, defineEmits, ref, computed, watch } from 'vue';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';

const props = defineProps({
  modelValue: {
    type: [String, Date],
    required: true,
  },
  message: String,
});

const emit = defineEmits(["update:modelValue", "update:age"]);

const form = ref({
  birthMonth: null,
  birthDay: "",
  birthYear: "",
});

// Month Options
const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

// Load initial modelValue into form
watch(
  () => props.modelValue,
  (newVal) => {
    if (newVal) {
      const d = new Date(newVal);
      form.value.birthMonth = months[d.getMonth()];
      form.value.birthDay = d.getDate().toString().padStart(2, "0");
      form.value.birthYear = d.getFullYear().toString();
    }
  },
  { immediate: true }
);

// Emit updated value as YYYY-MM-DD
watch(
  [() => form.value.birthMonth, () => form.value.birthDay, () => form.value.birthYear],
  () => {
    const day = parseInt(form.value.birthDay);
    const year = parseInt(form.value.birthYear);

    if (
      form.value.birthMonth &&
      !isNaN(day) &&
      day >= 1 &&
      day <= 31 &&
      !isNaN(year) &&
      form.value.birthYear.length === 4 &&
      year > 1900 &&
      year <= new Date().getFullYear()
    ) {
      const month = String(months.indexOf(form.value.birthMonth) + 1).padStart(2, "0");
      const d = String(day).padStart(2, "0");
      const birthString = `${year}-${month}-${d}`;

      emit("update:modelValue", birthString);
      emit("update:age", age.value);
    }
  }
);

// Age calculator
const age = computed(() => {
  const day = parseInt(form.value.birthDay);
  const year = parseInt(form.value.birthYear);

  if (!form.value.birthMonth || isNaN(day) || isNaN(year)) return null;
  if (form.value.birthYear.length !== 4) return null;

  const birthDate = new Date(
    year,
    months.indexOf(form.value.birthMonth),
    day
  );

  const today = new Date();
  let a = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();

  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    a--;
  }

  return a;
});

// ----------------------------
// DAY VALIDATION (1–31, 2 digits)
// ----------------------------
function validateDay(e) {
  let value = e.target.value;

  // digits only
  value = value.replace(/\D/g, "");

  // limit to 2 digits
  if (value.length > 2) value = value.slice(0, 2);

  // enforce 1–31
  let num = parseInt(value);
  if (num > 31) num = 31;
  if (num < 1 && value !== "") num = 1;

  form.value.birthDay = value === "" ? "" : num.toString();
}

// ----------------------------
// YEAR VALIDATION (4 digits only)
// ----------------------------
function validateYear(e) {
  let value = e.target.value;

  // digits only
  value = value.replace(/\D/g, "");

  // limit to 4 digits
  if (value.length > 4) value = value.slice(0, 4);

  form.value.birthYear = value;

  // auto calculate if 4 digits already typed
  if (value.length === 4) {
    emit("update:age", age.value);
  }
}

function onlyNumberKey(e) {
  // Block everything except digits
  const char = String.fromCharCode(e.keyCode);
  if (!/[0-9]/.test(char)) {
    e.preventDefault();
  }
}

</script>

<template>
  <div class="col-span-2 grid grid-cols-3 gap-3 w-full">

    <!-- Month Dropdown -->
    <Dropdown
      v-model="form.birthMonth"
      :options="months"
      class="w-full"
      placeholder="Month"
    />

    <!-- Day Input -->
    <InputText
      v-model="form.birthDay"
      class="w-full"
      placeholder="Day"
      type="text"
        @keypress="onlyNumberKey"
      maxlength="2"
      @input="validateDay"
    />

    <!-- Year Input -->
    <InputText
      v-model="form.birthYear"
      class="w-full"
      placeholder="Year"
      type="text"
        @keypress="onlyNumberKey"
      maxlength="4"
      @input="validateYear"
    />

    <!-- Age Display -->
    <p v-if="age !== null" class="text-sm font-semibold text-gray-700 col-span-3">
      Age is {{ age }}
    </p>
  </div>

  <small class="text-sm font-semibold text-red-500" v-if="message">
    {{ message }}
  </small>
</template>

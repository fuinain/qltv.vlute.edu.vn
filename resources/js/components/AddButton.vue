<template>
    <button type="button" class="btn btn-success" data-toggle="modal" :data-target="modalTarget">
      <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Thêm mới
    </button>
  </template>
  
  <script>
  export default {
    name: "AddButton",
    props: {
      modalTarget: {
        type: String,
        required: true
      }
    }
  }
  </script>
  
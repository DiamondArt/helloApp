<template>
  <div><h2>welcom to vue</h2>
    <button @click="counter++">click me{{counter}}</button>
  </div>
</template>

<script>
export default {
data: () =>({counter:0})
}
</script>

<style>

</style>
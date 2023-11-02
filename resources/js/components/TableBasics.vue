<script>
  export default{
    name:"TableBasic",
    props:['data','headings']
  }
</script>

<template>
  <VTable>
    <thead>
      <tr>
        <th class="text-uppercase">
          Desserts (100g Servings)
        </th>
        <th>
          calories
        </th>
        <th>
          Fat(g)
        </th>
        <th>
          Carbs(g)
        </th>
        <th>
          protein(g)
        </th>
      </tr>
    </thead>

    <tbody>
      <tr
        v-for="item in data"
        :key="item.dessert"
      >
        <td>
          {{ item.dessert }}
        </td>
        <td class="text-center">
          {{ item.calories }}
        </td>
        <td class="text-center">
          {{ item.fat }}
        </td>
        <td class="text-center">
          {{ item.carbs }}
        </td>
        <td class="text-center">
          {{ item.protein }}
        </td>
      </tr>
    </tbody>
  </VTable>
</template>

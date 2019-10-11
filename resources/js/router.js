import NotFoundComponent from './components/backend/NotFound.vue'
import ProductsIndex from './components/backend/products/ProductsIndex.vue'
import ProductsList from './components/backend/products/ProductsList.vue'
export const routes=[
    {
      path: '/products',
      component:ProductsIndex,
      children:[
        {path:'',component:ProductsList}
      ]
    },
    {
      path: '*',
      component:NotFoundComponent
    }
];

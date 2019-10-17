import NotFoundComponent from './components/backend/NotFound.vue'
import ProductsIndex from './components/backend/products/ProductsIndex.vue'
import ProductsList from './components/backend/products/ProductsList.vue'
import UsersIndex from './components/backend/users/UsersIndex.vue'
import UsersList from './components/backend/users/UsersList.vue'
import UserCreate from './components/backend/users/UserCreate.vue'
import RolesIndex from './components/backend/users/RolesIndex.vue'
import RolesList from './components/backend/users/RolesList.vue'
import RoleCreate from './components/backend/users/RoleCreate.vue'
import PricelistIndex from './components/backend/pricelist/PricelistIndex.vue'
import WarehouseIndex from './components/backend/warehouse/WarehouseIndex.vue'

export const routes=[
    {
      path: '/products',
      component:ProductsIndex,
      children:[
        {path:'',component:ProductsList}
      ]
    },
    {
      path: '/users',
      component:UsersIndex,
      children:[
        {path:'',component:UsersList},
        {path:'create',component:UserCreate},
        {
          path:'roles',
          component:RolesIndex,
          children:[
            {path:'',component:RolesList},
            {path:'create',component:RoleCreate},
          ]
        }
      ],
    },
    {
      path:'/pricelists', component:PricelistIndex
    },
    {
      path:'/warehouses', component:WarehouseIndex
    },
    {
      path: '*',
      component:NotFoundComponent
    }
];

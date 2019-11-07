import NotFoundComponent from './components/backend/NotFound.vue'
import ProductsIndex from './components/backend/products/ProductsIndex.vue'
import ProductsList from './components/backend/products/ProductsList.vue'
import UsersIndex from './components/backend/users/UsersIndex.vue'
import UsersList from './components/backend/users/UsersList.vue'
import RolesIndex from './components/backend/users/RolesIndex.vue'
import RolesList from './components/backend/users/RolesList.vue'
import PricelistIndex from './components/backend/pricelist/PricelistIndex.vue'
import WarehouseIndex from './components/backend/warehouse/WarehouseIndex.vue'
import TaxonomyIndex from './components/backend/taxonomy/TaxonomyIndex.vue'
import CategoryIndex from './components/backend/category/CategoryIndex.vue'
import CustomerIndex from './components/backend/customer/CustomerIndex.vue'
import SaleIndex from './components/backend/sale/SaleIndex.vue'
export const routes=[
    {path: '/products/:status?',component:ProductsList},
    {
      path: '/users',
      component:UsersIndex,
      children:[
        {path:'',component:UsersList},
        {
          path:'roles',
          component:RolesIndex,
          children:[
            {path:'',component:RolesList},
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
      path:'/taxonomies', component:TaxonomyIndex
    },
    {
      path:'/categories', component:CategoryIndex
    },
    {
      path:'/sales', component:SaleIndex
    },
    {
      path:'/customers/:status?', component:CustomerIndex
    },
    {
      path: '*',
      component:NotFoundComponent
    }
];

import Vue from 'vue'
import '@fortawesome/fontawesome-free/css/all.css'
import Vuetify, {
	VApp,
	VAppBar,
	VAppBarNavIcon,
	VToolbar,
	VToolbarItems,
	VToolbarTitle,
	VList,
    VListGroup,
    VListItem,
    VListItemAction,
    VListItemActionText,
    VListItemAvatar,
    VListItemContent,
    VListItemGroup,
    VListItemIcon,
    VListItemSubtitle,
    VListItemTitle,
    VMenu,
	VBtn,
	VIcon,
} from 'vuetify/lib'

Vue.use(Vuetify, {
	components: {
		VApp,
		VAppBar,
		VAppBarNavIcon,
		VToolbar,
		VToolbarItems,
		VToolbarTitle,
		VList,
	    VListGroup,
	    VListItem,
	    VListItemAction,
	    VListItemActionText,
	    VListItemAvatar,
	    VListItemContent,
	    VListItemGroup,
	    VListItemIcon,
	    VListItemSubtitle,
	    VListItemTitle,
	    VMenu,
		VBtn,
		VIcon,
	},
})
const opts = {
	icons: {
    	iconfont: 'fa', // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4'
  	},
}

export default new Vuetify(opts)
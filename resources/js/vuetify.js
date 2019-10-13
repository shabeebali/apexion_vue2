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
	VContainer,
    VCol,
    VRow,
    VSpacer,
    VLayout,
    VFlex,
    VContent,
    VCard,
    VCardTitle,
    VCardActions,
    VCardText,
    VTextField,
    VCheckbox,
    VNavigationDrawer,
    VDataTable,
    VDataTableHeader,
    VEditDialog,
    VAlert,
    VImg,
    VStepper,
    VStepperContent,
    VStepperStep,
    VStepperHeader,
    VStepperItems,
    VDialog,
    VForm,
    VDivider,
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
		VContainer,
	    VCol,
	    VRow,
	    VSpacer,
	    VLayout,
	    VFlex,
	    VContent,
	    VCard,
	    VCardTitle,
	    VCardActions,
	    VCardText,
	    VTextField,
	    VCheckbox,
	    VNavigationDrawer,
	    VDataTable,
	    VDataTableHeader,
	    VEditDialog,
	    VAlert,
	    VImg,
	    VStepper,
	    VStepperContent,
	    VStepperStep,
	    VStepperHeader,
	    VStepperItems,
	    VDialog,
	    VForm,
	    VDivider,
	},
})
const opts = {
	icons: {
    	iconfont: 'fa', // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4'
  	},
}

export default new Vuetify(opts)
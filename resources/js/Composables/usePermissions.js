import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
    const page = usePage()
    
    const permissions = computed(() => {
        return page.props.permissions || []
    })

    const hasPermission = (permission) => {
        return permissions.value.includes(permission)
    }

    const hasAnyPermission = (permissionArray) => {
        return permissionArray.some(permission => hasPermission(permission))
    }

    const hasAllPermissions = (permissionArray) => {
        return permissionArray.every(permission => hasPermission(permission))
    }

    return {
        permissions,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions
    }
}
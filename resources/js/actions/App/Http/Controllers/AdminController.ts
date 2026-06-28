import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::dashboard
* @see app/Http/Controllers/AdminController.php:20
* @route '/admin'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::dashboard
* @see app/Http/Controllers/AdminController.php:20
* @route '/admin'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::dashboard
* @see app/Http/Controllers/AdminController.php:20
* @route '/admin'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::dashboard
* @see app/Http/Controllers/AdminController.php:20
* @route '/admin'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::dashboard
* @see app/Http/Controllers/AdminController.php:20
* @route '/admin'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::dashboard
* @see app/Http/Controllers/AdminController.php:20
* @route '/admin'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::dashboard
* @see app/Http/Controllers/AdminController.php:20
* @route '/admin'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \App\Http\Controllers\AdminController::toggleLock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
export const toggleLock = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleLock.url(options),
    method: 'post',
})

toggleLock.definition = {
    methods: ["post"],
    url: '/admin/lock',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::toggleLock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
toggleLock.url = (options?: RouteQueryOptions) => {
    return toggleLock.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::toggleLock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
toggleLock.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleLock.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::toggleLock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
const toggleLockForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleLock.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::toggleLock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
toggleLockForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleLock.url(options),
    method: 'post',
})

toggleLock.form = toggleLockForm

const AdminController = { dashboard, toggleLock }

export default AdminController
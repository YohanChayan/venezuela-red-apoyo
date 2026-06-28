import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import users from './users'
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
* @see \App\Http\Controllers\AdminController::lock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
export const lock = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: lock.url(options),
    method: 'post',
})

lock.definition = {
    methods: ["post"],
    url: '/admin/lock',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::lock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
lock.url = (options?: RouteQueryOptions) => {
    return lock.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::lock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
lock.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: lock.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::lock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
const lockForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: lock.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::lock
* @see app/Http/Controllers/AdminController.php:42
* @route '/admin/lock'
*/
lockForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: lock.url(options),
    method: 'post',
})

lock.form = lockForm

const admin = {
    dashboard: Object.assign(dashboard, dashboard),
    lock: Object.assign(lock, lock),
    users: Object.assign(users, users),
}

export default admin